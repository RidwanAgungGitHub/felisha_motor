<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Exports\BarangKeluarExport; // TAMBAHKAN INI
use App\Imports\BarangKeluarImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel; // TAMBAHKAN INI
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class BarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil bulan dan tahun dari request atau menggunakan bulan dan tahun saat ini
        $bulan = $request->input('bulan', now()->format('m'));
        $tahun = $request->input('tahun', now()->format('Y'));

        // Data untuk tabel pertama (semua histori barang keluar) dengan pagination
        $barangKeluarQuery = BarangKeluar::with('barang');
        if ($bulan && $tahun) {
            $barangKeluarQuery->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun);
        }

        // Pagination untuk histori barang keluar - 2 data per halaman
        $barangKeluar = $barangKeluarQuery->latest()->paginate(10, ['*'], 'histori_page');

        // Preserve query parameters for pagination links
        $barangKeluar->appends([
            'bulan' => $bulan,
            'tahun' => $tahun,
            'tab' => $request->input('tab', 'histori')
        ]);

        // Menghitung jumlah hari aktif (hari dimana ada transaksi) untuk setiap barang
        $hariAktifPerBarang = [];
        $totalHariAktif = 0;

        // Dapatkan tanggal awal dan akhir bulan
        $awalBulan = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
        $akhirBulan = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->endOfDay();

        // Dapatkan hari aktif untuk perhitungan keseluruhan (jumlah hari unik dengan transaksi)
        $hariAktifGlobal = DB::table('barang_keluar')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->selectRaw('COUNT(DISTINCT DATE(tanggal)) as jumlah_hari_aktif')
            ->first();

        $totalHariAktif = $hariAktifGlobal->jumlah_hari_aktif ?? 1; // Default to 1 to avoid division by zero

        // Jika tidak ada hari aktif (tidak ada transaksi bulan ini), gunakan 1 untuk menghindari division by zero
        if ($totalHariAktif == 0) {
            $totalHariAktif = 1;
        }

        // Hitung hari aktif per barang (jumlah hari unik dengan transaksi untuk setiap barang)
        $hariAktifPerBarangData = DB::table('barang_keluar')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->groupBy('barang_id')
            ->select(
                'barang_id',
                DB::raw('COUNT(DISTINCT DATE(tanggal)) as jumlah_hari_aktif')
            )
            ->get();

        foreach ($hariAktifPerBarangData as $item) {
            $hariAktifPerBarang[$item->barang_id] = $item->jumlah_hari_aktif > 0 ? $item->jumlah_hari_aktif : 1;
        }

        // Data untuk tabel kedua (dikelompokkan per barang per bulan) dengan pagination
        $barangKeluarBulananQuery = DB::table('barang_keluar')
            ->join('barang', 'barang_keluar.barang_id', '=', 'barang.id')
            ->select(
                'barang.id as barang_id',
                'barang.nama_barang',
                'barang.merek',
                'barang.satuan',
                DB::raw('SUM(barang_keluar.jumlah) as total_jumlah'),
                DB::raw('SUM(barang_keluar.total_harga) as total_harga'),
                DB::raw("DATE_FORMAT(barang_keluar.tanggal, '%m/%Y') as bulan")
            )
            ->whereMonth('barang_keluar.tanggal', $bulan)
            ->whereYear('barang_keluar.tanggal', $tahun)
            ->groupBy('barang.id', 'barang.nama_barang', 'barang.merek', 'barang.satuan', 'bulan')
            ->orderBy('barang.nama_barang');

        // Pagination untuk ringkasan barang keluar - 2 data per halaman
        $barangKeluarBulanan = $barangKeluarBulananQuery->paginate(10, ['*'], 'ringkasan_page');

        // Preserve query parameters for pagination links
        $barangKeluarBulanan->appends([
            'bulan' => $bulan,
            'tahun' => $tahun,
            'tab' => $request->input('tab', 'histori')
        ]);

        // Menghitung rata-rata per hari aktif untuk setiap item
        $totalJumlah = 0;
        $totalHarga = 0;

        // Hitung total dari semua data (tidak hanya yang ada di halaman saat ini)
        $allRingkasanData = DB::table('barang_keluar')
            ->join('barang', 'barang_keluar.barang_id', '=', 'barang.id')
            ->select(
                'barang.id as barang_id',
                DB::raw('SUM(barang_keluar.jumlah) as total_jumlah'),
                DB::raw('SUM(barang_keluar.total_harga) as total_harga')
            )
            ->whereMonth('barang_keluar.tanggal', $bulan)
            ->whereYear('barang_keluar.tanggal', $tahun)
            ->groupBy('barang.id')
            ->get();

        foreach ($allRingkasanData as $item) {
            $totalJumlah += $item->total_jumlah;
            $totalHarga += $item->total_harga;
        }

        // Proses data yang ada di halaman saat ini
        foreach ($barangKeluarBulanan as $item) {
            // Menggunakan jumlah hari aktif transaksi untuk barang ini, default = 1 jika tidak ada
            $hariAktif = $hariAktifPerBarang[$item->barang_id] ?? 1;

            $item->jumlah_hari_aktif = $hariAktif;
            $item->rata_per_hari = ceil($item->total_jumlah / $hariAktif);
        }

        // Mengambil daftar bulan dan tahun yang tersedia
        $availableMonths = DB::table('barang_keluar')
            ->select(DB::raw('DISTINCT MONTH(tanggal) as bulan'))
            ->orderBy('bulan')
            ->get()
            ->pluck('bulan')
            ->toArray();

        $availableYears = DB::table('barang_keluar')
            ->select(DB::raw('DISTINCT YEAR(tanggal) as tahun'))
            ->orderBy('tahun', 'desc')
            ->get()
            ->pluck('tahun')
            ->toArray();

        // Jika tidak ada data, tetapkan nilai default
        if (empty($availableMonths)) {
            $availableMonths = [now()->format('m')];
        }

        if (empty($availableYears)) {
            $availableYears = [now()->format('Y')];
        }

        // Variabel untuk tab aktif
        $activeTab = $request->input('tab', 'histori');

        // Menghitung total rata-rata per hari
        $totalRataPerHari = $totalHariAktif > 0 ? ceil($totalJumlah / $totalHariAktif) : 0;

        return view('barang_keluar.index', compact(
            'barangKeluar',
            'barangKeluarBulanan',
            'bulan',
            'tahun',
            'availableMonths',
            'availableYears',
            'activeTab',
            'totalJumlah',
            'totalRataPerHari',
            'totalHarga',
            'totalHariAktif'
        ));
    }

    public function getBarang(Request $request)
    {
        $search = $request->search;

        if ($search == '') {
            $barang = Barang::where('stok', '>', 0)->limit(5)->get();
        } else {
            $barang = Barang::where('stok', '>', 0)
                ->where(function ($query) use ($search) {
                    $query->where('nama_barang', 'like', '%' . $search . '%')
                        ->orWhere('merek', 'like', '%' . $search . '%');
                })
                ->limit(5)
                ->get();
        }

        $response = array();
        foreach ($barang as $item) {
            $response[] = array(
                'id' => $item->id,
                'text' => $item->nama_barang . ' - ' . $item->merek,
                'nama_barang' => $item->nama_barang,
                'merek' => $item->merek,
                'harga' => $item->harga,
                'stok' => $item->stok,
                'satuan' => $item->satuan
            );
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date',
        ]);

        // Mulai transaksi DB untuk memastikan konsistensi data
        DB::beginTransaction();

        try {
            $barang = Barang::findOrFail($request->barang_id);

            // Validasi stok cukup
            if ($barang->stok < $request->jumlah) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['jumlah' => 'Stok tidak mencukupi. Stok tersedia: ' . $barang->stok]);
            }

            // Hitung total harga
            $totalHarga = $barang->harga * $request->jumlah;

            // Simpan barang keluar
            BarangKeluar::create([
                'barang_id' => $request->barang_id,
                'jumlah' => $request->jumlah,
                'tanggal' => $request->tanggal,
                'total_harga' => $totalHarga,
                'keterangan' => $request->keterangan ?? null,
            ]);

            // Update stok barang
            $barang->stok -= $request->jumlah;
            $barang->save();

            DB::commit();

            // Redirect ke halaman yang sama dengan tab dan filter yang sama
            $redirectParams = [
                'bulan' => date('m', strtotime($request->tanggal)),
                'tahun' => date('Y', strtotime($request->tanggal)),
                'tab' => 'ringkasan'  // Mengarahkan ke tab ringkasan untuk melihat perubahan
            ];

            return redirect()->route('barang-keluar.index', $redirectParams)
                ->with('success', 'Barang keluar berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function show(BarangKeluar $barangKeluar)
    {
        $barangKeluar->load('barang');
        return view('barang_keluar.show', compact('barangKeluar'));
    }

    // Fungsi laporan barang keluar
    public function laporan(Request $request)
    {
        $tanggalMulai = $request->tanggal_mulai ?? date('Y-m-01');
        $tanggalAkhir = $request->tanggal_akhir ?? date('Y-m-d');

        $data = BarangKeluar::with('barang')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->latest()
            ->paginate(10);

        // Hitung ringkasan laporan
        $laporan = [
            'jumlah_transaksi' => BarangKeluar::whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])->count(),
            'total_pendapatan' => BarangKeluar::whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])->sum('total_harga'),
            'jumlah_barang' => BarangKeluar::whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])->sum('jumlah'),
        ];

        return view('barang_keluar.laporan', compact('data', 'laporan'));
    }

    public function export(Request $request)
{
    $bulan = $request->input('bulan', now()->format('m'));
    $tahun = $request->input('tahun', now()->format('Y'));

    $namaBulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
    ];

    $fileName = 'Histori_Barang_Keluar_' . $namaBulan[$bulan] . '_' . $tahun . '.xlsx';

    return Excel::download(new BarangKeluarExport($bulan, $tahun), $fileName);
}

// Method untuk import
public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:2048'
    ], [
        'file.required' => 'File wajib dipilih',
        'file.mimes' => 'File harus berformat .xlsx, .xls, atau .csv',
        'file.max' => 'Ukuran file maksimal 2MB'
    ]);

    try {
        $import = new BarangKeluarImport();
        Excel::import($import, $request->file('file'));

        $summary = $import->getSummary();

        if ($summary['success_count'] > 0) {
            $message = "Import berhasil! {$summary['success_count']} data berhasil diimport";

            if ($summary['failed_count'] > 0) {
                $message .= ", {$summary['failed_count']} data gagal diimport.";
                return redirect()->back()
                    ->with('success', $message)
                    ->with('import_errors', $summary['errors']);
            }

            return redirect()->back()->with('success', $message);
        } else {
            return redirect()->back()
                ->with('error', 'Tidak ada data yang berhasil diimport')
                ->with('import_errors', $summary['errors']);
        }

    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        $failures = $e->failures();
        $errors = [];

        foreach ($failures as $failure) {
            $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
        }

        return redirect()->back()
            ->with('error', 'Validasi gagal')
            ->with('import_errors', $errors);

    } catch (\Exception $e) {
        \Log::error('Error import barang keluar: ' . $e->getMessage());

        return redirect()->back()
            ->with('error', 'Error saat import: ' . $e->getMessage());
    }
}
}
