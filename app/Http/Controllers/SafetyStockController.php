<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\SafetyStock;
use App\Models\ReorderPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SafetyStockController extends Controller
{
    public function index()
    {
        $safetyStocks = SafetyStock::with('barang')->latest()->get();
        return view('safety_stock.index', compact('safetyStocks'));
    }

    public function create(Request $request)
    {
        $barangs = Barang::where('stok', '>', 0)->get();
        $selectedBarangId = $request->input('barang_id');
        $pemakaianMaksimum = 0;
        $pemakaianRataRata = 0;
        $bulanTahun = null;

        // Default to current month and year
        $bulan = $request->input('bulan', now()->format('m'));
        $tahun = $request->input('tahun', now()->format('Y'));

        if ($selectedBarangId) {
            // Get data from ringkasan barang keluar
            $barangData = $this->getDataFromRingkasan($selectedBarangId, $bulan, $tahun);

            $pemakaianMaksimum = $barangData->pemakaian_maksimum;
            $pemakaianRataRata = $barangData->pemakaian_rata_rata;
            $bulanTahun = $barangData->bulan_tahun;
        }

        // Mengambil daftar bulan dan tahun yang tersedia dari barang keluar
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

        return view('safety_stock.create', compact(
            'barangs',
            'selectedBarangId',
            'pemakaianMaksimum',
            'pemakaianRataRata',
            'bulanTahun',
            'bulan',
            'tahun',
            'availableMonths',
            'availableYears'
        ));
    }

    private function getDataFromRingkasan($barangId, $bulan, $tahun)
    {
        // Ensure we have valid values
        if (empty($bulan)) $bulan = now()->format('m');
        if (empty($tahun)) $tahun = now()->format('Y');

        // Get count of active days (days with transactions) for this specific product
        $hariAktif = DB::table('barang_keluar')
            ->where('barang_id', $barangId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->selectRaw('COUNT(DISTINCT DATE(tanggal)) as jumlah_hari_aktif')
            ->first();

        $jumlahHariAktif = $hariAktif->jumlah_hari_aktif ?? 1; // Default to 1 to avoid division by zero

        // Get total usage for the month - this is what we need for "pemakaian maksimum"
        $totalPemakaian = DB::table('barang_keluar')
            ->where('barang_id', $barangId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->sum('jumlah') ?? 0;

        // For pemakaian maksimum, we'll use the total usage in a month from ringkasan
        $pemakaianMaksimum = $totalPemakaian;

        // Calculate average usage per active day (same as in ringkasan barang keluar)
        $pemakaianRataRata = $jumlahHariAktif > 0 ? ceil($totalPemakaian / $jumlahHariAktif) : 0;

        // Format month/year in MM/YYYY format for display
        $bulanTahun = sprintf("%02d/%04d", $bulan, $tahun);

        return (object)[
            'pemakaian_maksimum' => $pemakaianMaksimum,
            'pemakaian_rata_rata' => $pemakaianRataRata,
            'bulan_tahun' => $bulanTahun
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'pemakaian_maksimum' => 'required|numeric|min:0',
            'pemakaian_rata_rata' => 'required|numeric|min:0',
            'lead_time' => 'required|numeric|min:0',
        ]);

        // Default to current month and year in MM/YYYY format if bulan is not provided
        $bulan = $request->input('bulan');
        if (empty($bulan)) {
            $bulan = now()->format('m/Y');
        }

        // Hitung safety stock
        $hasil = ($request->pemakaian_maksimum - $request->pemakaian_rata_rata) * $request->lead_time;

        SafetyStock::create([
            'barang_id' => $request->barang_id,
            'pemakaian_maksimum' => $request->pemakaian_maksimum,
            'pemakaian_rata_rata' => $request->pemakaian_rata_rata,
            'lead_time' => $request->lead_time,
            'hasil' => $hasil,
            'bulan' => $bulan
        ]);

        return redirect()->route('safety-stock.index')
            ->with('success', 'Safety Stock berhasil ditambahkan.');
    }

    // Menggabungkan fungsi calculate dan refresh menjadi satu
    public function recalculate(Request $request, $id)
    {
        $safetyStock = SafetyStock::findOrFail($id);
        $barangId = $safetyStock->barang_id;

        // Default to current month and year if bulan field is not set
        $bulan = now()->format('m');
        $tahun = now()->format('Y');

        // Extract month and year from bulan field (format: MM/YYYY)
        if (!empty($safetyStock->bulan) && strpos($safetyStock->bulan, '/') !== false) {
            $bulanTahunArray = explode('/', $safetyStock->bulan);
            if (count($bulanTahunArray) >= 2) {
                $bulan = $bulanTahunArray[0];
                $tahun = $bulanTahunArray[1];
            }
        }

        // Get updated data from ringkasan
        $barangData = $this->getDataFromRingkasan($barangId, $bulan, $tahun);

        // Update data in safety stock with fresh data
        $safetyStock->pemakaian_maksimum = $barangData->pemakaian_maksimum;
        $safetyStock->pemakaian_rata_rata = $barangData->pemakaian_rata_rata;
        $safetyStock->bulan = $barangData->bulan_tahun; // Update the bulan field with the formatted month/year

        // Recalculate safety stock result
        $hasil = ($barangData->pemakaian_maksimum - $barangData->pemakaian_rata_rata) * $safetyStock->lead_time;
        $safetyStock->hasil = $hasil;

        $safetyStock->save();

        return redirect()->route('safety-stock.index')
            ->with('success', 'Data pemakaian berhasil diperbarui dan Safety Stock berhasil dihitung ulang.');
    }

    public function edit($id)
    {
        $safetyStock = SafetyStock::with('barang')->findOrFail($id);
        return view('safety_stock.edit', compact('safetyStock'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lead_time' => 'required|numeric|min:0',
        ]);

        $safetyStock = SafetyStock::findOrFail($id);

        // Hanya update lead time
        $safetyStock->lead_time = $request->lead_time;

        // Hitung ulang safety stock
        $hasil = ($safetyStock->pemakaian_maksimum - $safetyStock->pemakaian_rata_rata) * $request->lead_time;
        $safetyStock->hasil = $hasil;

        $safetyStock->save();

        return redirect()->route('safety-stock.index')
            ->with('success', 'Safety Stock berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $safetyStock = SafetyStock::findOrFail($id);

            // Check if this safety stock is referenced in any reorder point
            $reorderPointExists = ReorderPoint::where('safety_stock', $safetyStock->hasil)
                ->where('barang_id', $safetyStock->barang_id)
                ->exists();

            if ($reorderPointExists) {
                return redirect()->route('safety-stock.index')
                    ->with('error', 'Safety Stock tidak dapat dihapus karena sedang digunakan di Reorder Point.');
            }

            $safetyStock->delete();

            return redirect()->route('safety-stock.index')
                ->with('success', 'Safety Stock berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('safety-stock.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}
