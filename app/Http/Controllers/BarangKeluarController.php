<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil bulan dan tahun dari request atau menggunakan bulan dan tahun saat ini
        $bulan = $request->input('bulan', now()->format('m'));
        $tahun = $request->input('tahun', now()->format('Y'));

        // Data untuk tabel pertama (semua histori barang keluar)
        // Filter berdasarkan bulan dan tahun jika ada parameter
        $barangKeluarQuery = BarangKeluar::with('barang');
        if ($bulan && $tahun) {
            $barangKeluarQuery->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun);
        }
        $barangKeluar = $barangKeluarQuery->latest()->get();

        // Data untuk tabel kedua (dikelompokkan per bulan)
        // Menggunakan query builder untuk mengelompokkan data
        $barangKeluarBulanan = DB::table('barang_keluar')
            ->join('barang', 'barang_keluar.barang_id', '=', 'barang.id')
            ->select(
                'barang.nama_barang',
                'barang.merek',
                'barang.satuan',
                DB::raw('SUM(barang_keluar.jumlah) as total_jumlah'),
                DB::raw('SUM(barang_keluar.total_harga) as total_harga'),
                DB::raw("DATE_FORMAT(barang_keluar.tanggal, '%m/%Y') as bulan"),
                // Hitung jumlah hari dalam bulan yang dipilih
                DB::raw("DAY(LAST_DAY(CONCAT('$tahun-$bulan-01'))) as jumlah_hari"),
                // Hitung rata-rata per hari (dibulatkan ke integer)
                DB::raw("CEILING(SUM(barang_keluar.jumlah) / DAY(LAST_DAY(CONCAT('$tahun-$bulan-01')))) as rata_per_hari")
            )
            ->whereMonth('barang_keluar.tanggal', $bulan)
            ->whereYear('barang_keluar.tanggal', $tahun)
            ->groupBy('barang.nama_barang', 'barang.merek', 'barang.satuan', 'bulan')
            ->orderBy('barang.nama_barang')
            ->get();

        // Mengambil daftar bulan yang tersedia untuk dropdown filter
        $availableMonths = DB::table('barang_keluar')
            ->select(DB::raw('DISTINCT MONTH(tanggal) as bulan'))
            ->orderBy('bulan')
            ->get()
            ->pluck('bulan')
            ->toArray();

        // Mengambil daftar tahun yang tersedia untuk dropdown filter
        $availableYears = DB::table('barang_keluar')
            ->select(DB::raw('DISTINCT YEAR(tanggal) as tahun'))
            ->orderBy('tahun', 'desc')
            ->get()
            ->pluck('tahun')
            ->toArray();

        // Jika tidak ada data tersedia, tetapkan nilai default
        if (empty($availableMonths)) {
            $availableMonths = [now()->format('m')];
        }

        if (empty($availableYears)) {
            $availableYears = [now()->format('Y')];
        }

        // Variabel untuk tab aktif (disimpan di session atau dari request)
        $activeTab = $request->input('tab', 'histori');

        return view('barang_keluar.index', compact(
            'barangKeluar',
            'barangKeluarBulanan',
            'bulan',
            'tahun',
            'availableMonths',
            'availableYears',
            'activeTab'
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

            return redirect()->route('barang-keluar.index')
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

    // Fungsi untuk menampilkan halaman kasir
    public function kasir(Request $request)
    {
        $data = Barang::where('stok', '>', 0)->get();
        return view('barang_keluar.kasir', compact('data'));
    }

    // Fungsi untuk menambahkan item ke keranjang
    public function addToCart(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
        ]);

        $barang_id = $request->barang_id;
        $barang = Barang::findOrFail($barang_id);

        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan');
        }

        $cart = session()->get('cart', []);

        // Jika barang sudah ada di keranjang, tambahkan jumlahnya
        if (isset($cart[$barang_id])) {
            // Periksa apakah jumlah yang ditambahkan melebihi stok
            if ($cart[$barang_id]['jumlah'] + 1 > $barang->stok) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $barang->stok);
            }
            $cart[$barang_id]['jumlah']++;
        } else {
            // Jika belum ada, tambahkan barang baru ke keranjang
            $cart[$barang_id] = [
                'id' => $barang->id,
                'kode' => $barang->id, // Menggunakan ID sebagai kode
                'nama_barang' => $barang->nama_barang . ' - ' . $barang->merek,
                'merek' => $barang->merek,
                'harga' => $barang->harga,
                'jumlah' => 1,
                'stok_tersedia' => $barang->stok
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }

    // Fungsi untuk mengubah jumlah item di keranjang
    public function updateCartQty(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'jumlah' => 'required|integer|min:1',
        ]);

        $id = $request->id;
        $jumlah = $request->jumlah;

        $cart = session()->get('cart');

        if ($jumlah <= 0) {
            return $this->removeFromCart($request);
        }

        if (isset($cart[$id])) {
            // Periksa apakah jumlah yang diupdate melebihi stok
            if ($jumlah > $cart[$id]['stok_tersedia']) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $cart[$id]['stok_tersedia']);
            }

            $cart[$id]['jumlah'] = $jumlah;
            session()->put('cart', $cart);
        }

        return redirect()->back();
    }

    // Fungsi untuk menghapus item dari keranjang
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $id = $request->id;
        $cart = session()->get('cart');

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back();
    }

    // Fungsi untuk membersihkan keranjang
    public function clearCart()
    {
        session()->forget('cart');
        return redirect()->back()->with('success', 'Keranjang berhasil dikosongkan');
    }

    // Fungsi untuk checkout
    public function checkout(Request $request)
    {
        $request->validate([
            'tunai' => 'required|numeric|min:0',
            'nama_pelanggan' => 'nullable|string|max:255',
            'no_whatsapp' => 'nullable|string|max:20',
        ]);

        $cart = session()->get('cart');

        if (!$cart || count($cart) == 0) {
            return redirect()->back()->with('error', 'Keranjang belanja masih kosong');
        }

        // Hitung total transaksi
        $totalTransaksi = 0;
        foreach ($cart as $item) {
            $totalTransaksi += $item['harga'] * $item['jumlah'];
        }

        // Validasi jumlah tunai
        if ($request->tunai < $totalTransaksi) {
            return redirect()->back()->with('error', 'Jumlah tunai kurang dari total belanja');
        }

        // Generate invoice number
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . Str::random(5);

        // Begin transaction
        DB::beginTransaction();

        try {
            foreach ($cart as $id => $item) {
                $barang = Barang::findOrFail($id);

                // Validasi stok terakhir
                if ($barang->stok < $item['jumlah']) {
                    throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi. Stok tersedia: {$barang->stok}");
                }

                // Hitung total harga
                $totalHarga = $barang->harga * $item['jumlah'];

                // Simpan barang keluar
                BarangKeluar::create([
                    'barang_id' => $id,
                    'jumlah' => $item['jumlah'],
                    'tanggal' => now(),
                    'total_harga' => $totalHarga,
                    'keterangan' => 'Penjualan kasir - ' . $invoiceNumber .
                        ($request->nama_pelanggan ? ' - Pelanggan: ' . $request->nama_pelanggan : ''),
                ]);

                // Update stok barang
                $barang->stok -= $item['jumlah'];
                $barang->save();
            }

            DB::commit();

            // Simpan informasi checkout untuk halaman struk
            $checkout = [
                'invoice_number' => $invoiceNumber,
                'tanggal' => now()->format('Y-m-d H:i:s'),
                'nama_pelanggan' => $request->nama_pelanggan ?? 'Pelanggan Umum',
                'no_whatsapp' => $request->no_whatsapp,
                'items' => $cart,
                'total' => $totalTransaksi,
                'tunai' => $request->tunai,
                'kembalian' => $request->tunai - $totalTransaksi
            ];

            session()->put('checkout', $checkout);

            // Kosongkan keranjang
            session()->forget('cart');

            return redirect()->route('kasir.struk');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Fungsi untuk menampilkan struk
    public function struk()
    {
        $checkout = session()->get('checkout');

        if (!$checkout) {
            return redirect()->route('kasir')->with('error', 'Data transaksi tidak ditemukan');
        }

        return view('barang_keluar.struk', compact('checkout'));
    }

    // Fungsi untuk menghitung kembalian (diakses dengan GET)
    public function hitungKembalian(Request $request)
    {
        $request->validate([
            'tunai' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        $tunai = $request->tunai;
        $total = $request->total;
        $kembalian = $tunai - $total;

        return redirect()->route('kasir')->with('kembalian', $kembalian);
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

    // Fungsi cetak laporan
    public function cetakLaporan(Request $request)
    {
        $tanggalMulai = $request->tanggal_mulai ?? date('Y-m-01');
        $tanggalAkhir = $request->tanggal_akhir ?? date('Y-m-d');

        $data = BarangKeluar::with('barang')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->latest()
            ->get();

        // Hitung ringkasan laporan
        $laporan = [
            'jumlah_transaksi' => BarangKeluar::whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])->count(),
            'total_pendapatan' => BarangKeluar::whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])->sum('total_harga'),
            'jumlah_barang' => BarangKeluar::whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])->sum('jumlah'),
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_akhir' => $tanggalAkhir
        ];

        return view('barang_keluar.cetak_laporan', compact('data', 'laporan'));
    }
}
