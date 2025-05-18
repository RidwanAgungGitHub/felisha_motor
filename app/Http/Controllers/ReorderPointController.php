<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\SafetyStock;
use App\Models\ReorderPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ReorderPointController extends Controller
{
    public function index()
    {
        $reorderPoints = ReorderPoint::with('barang')->latest()->get();
        return view('reorder_point.index', compact('reorderPoints'));
    }

    public function create()
    {
        $barangs = Barang::where('stok', '>', 0)->get();
        return view('reorder_point.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'safety_stock' => 'required|numeric|min:0',
            'lead_time' => 'required|numeric|min:0',
        ]);

        // Ambil data pemakaian rata-rata
        $bulan = now()->format('m');
        $tahun = now()->format('Y');

        $pemakaianRataRata = DB::table('barang_keluar')
            ->select(DB::raw('CEILING(AVG(jumlah)) as rata_rata'))
            ->where('barang_id', $request->barang_id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->first()->rata_rata ?? 0;

        // Hitung reorder point: ROP = SS + (LT × d)
        $hasil = $request->safety_stock + ($request->lead_time * $pemakaianRataRata);

        ReorderPoint::create([
            'barang_id' => $request->barang_id,
            'safety_stock' => $request->safety_stock,
            'lead_time' => $request->lead_time,
            'hasil' => $hasil
        ]);

        return redirect()->route('reorder-point.index')
            ->with('success', 'Reorder Point berhasil ditambahkan.');
    }

    public function getSafetyStock(Request $request)
    {
        $barangId = $request->barang_id;

        // Ambil safety stock terakhir untuk barang ini
        $safetyStock = SafetyStock::where('barang_id', $barangId)
            ->latest()
            ->first();

        $data = [
            'safety_stock' => $safetyStock ? $safetyStock->hasil : 0
        ];

        return response()->json($data);
    }

    public function calculate(Request $request, $id)
    {
        $reorderPoint = ReorderPoint::findOrFail($id);

        // Ambil data pemakaian rata-rata
        $bulan = now()->format('m');
        $tahun = now()->format('Y');

        $pemakaianRataRata = DB::table('barang_keluar')
            ->select(DB::raw('CEILING(AVG(jumlah)) as rata_rata'))
            ->where('barang_id', $reorderPoint->barang_id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->first()->rata_rata ?? 0;

        // Hitung ulang reorder point
        $hasil = $reorderPoint->safety_stock + ($reorderPoint->lead_time * $pemakaianRataRata);

        $reorderPoint->hasil = $hasil;
        $reorderPoint->save();

        return redirect()->route('reorder-point.index')
            ->with('success', 'Reorder Point berhasil dihitung.');
    }
}
