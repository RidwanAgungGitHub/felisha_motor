<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\SafetyStock;
use App\Models\ReorderPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SafetyStockController extends Controller
{
    public function index()
    {
        $safetyStocks = SafetyStock::with('barang')->latest()->get();
        return view('safety_stock.index', compact('safetyStocks'));
    }

    public function create()
    {
        $barangs = Barang::where('stok', '>', 0)->get();
        return view('safety_stock.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'pemakaian_maksimum' => 'required|numeric|min:0',
            'pemakaian_rata_rata' => 'required|numeric|min:0',
            'lead_time' => 'required|numeric|min:0',
        ]);

        // Hitung safety stock
        $hasil = ($request->pemakaian_maksimum - $request->pemakaian_rata_rata) * $request->lead_time;

        SafetyStock::create([
            'barang_id' => $request->barang_id,
            'pemakaian_maksimum' => $request->pemakaian_maksimum,
            'pemakaian_rata_rata' => $request->pemakaian_rata_rata,
            'lead_time' => $request->lead_time,
            'hasil' => $hasil
        ]);

        return redirect()->route('safety-stock.index')
            ->with('success', 'Safety Stock berhasil ditambahkan.');
    }

    public function getBarangData(Request $request)
    {
        $barangId = $request->barang_id;
        $bulan = $request->bulan ?? now()->format('m');
        $tahun = $request->tahun ?? now()->format('Y');

        // Periksa apakah ada data barang keluar untuk barang ini
        $barangKeluarExist = BarangKeluar::where('barang_id', $barangId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->exists();

        if ($barangKeluarExist) {
            // Jika ada data, ambil data dari tabel barang_keluar yang sudah dikelompokkan
            $barangData = DB::table('barang_keluar')
                ->select(
                    DB::raw('MAX(jumlah) as pemakaian_maksimum'),
                    DB::raw('CEILING(AVG(jumlah)) as pemakaian_rata_rata')
                )
                ->where('barang_id', $barangId)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->first();
        } else {
            // Jika tidak ada data, gunakan nilai default
            $barangData = (object)[
                'pemakaian_maksimum' => 0,
                'pemakaian_rata_rata' => 0
            ];
        }

        return response()->json($barangData);
    }

    public function calculate(Request $request, $id)
    {
        $safetyStock = SafetyStock::findOrFail($id);

        // Hitung ulang safety stock
        $hasil = ($safetyStock->pemakaian_maksimum - $safetyStock->pemakaian_rata_rata) * $safetyStock->lead_time;

        $safetyStock->hasil = $hasil;
        $safetyStock->save();

        return redirect()->route('safety-stock.index')
            ->with('success', 'Safety Stock berhasil dihitung.');
    }
}
