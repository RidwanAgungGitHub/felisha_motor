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

    public function create(Request $request)
    {
        $barangs = Barang::where('stok', '>', 0)->get();
        $selectedBarangId = $request->input('barang_id');
        $safetyStock = 0;
        $period = '';

        if ($selectedBarangId) {
            // Ambil safety stock terakhir untuk barang ini
            $safetyStockData = SafetyStock::where('barang_id', $selectedBarangId)
                ->latest()
                ->first();

            $safetyStock = $safetyStockData ? $safetyStockData->hasil : 0;
            $period = $safetyStockData ? $safetyStockData->bulan : '';
        }

        return view('reorder_point.create', compact(
            'barangs',
            'selectedBarangId',
            'safetyStock',
            'period'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'safety_stock' => 'required|numeric|min:0',
            'period' => 'required|string',
            'lead_time' => 'required|numeric|min:0',
            'permintaan_per_periode' => 'required|numeric|min:0',
            'total_hari_kerja' => 'required|numeric|min:1',
        ]);

        // Hitung permintaan harian (d/hari kerja)
        $permintaanHarian = $request->permintaan_per_periode / $request->total_hari_kerja;

        // Hitung reorder point: ROP = SS + (LT × (d/hari kerja))
        $hasil = $request->safety_stock + ($request->lead_time * $permintaanHarian);

        ReorderPoint::create([
            'barang_id' => $request->barang_id,
            'safety_stock' => $request->safety_stock,
            'period' => $request->period,
            'lead_time' => $request->lead_time,
            'permintaan_per_periode' => $request->permintaan_per_periode,
            'total_hari_kerja' => $request->total_hari_kerja,
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
            'safety_stock' => $safetyStock ? $safetyStock->hasil : 0,
            'period' => $safetyStock ? $safetyStock->bulan : ''
        ];

        return response()->json($data);
    }

    // Menggabungkan fungsi refresh safety stock dan hitung ulang reorder point
    public function recalculate(Request $request, $id)
    {
        $reorderPoint = ReorderPoint::findOrFail($id);
        $barangId = $reorderPoint->barang_id;

        // Ambil safety stock terbaru untuk barang ini
        $latestSafetyStock = SafetyStock::where('barang_id', $barangId)
            ->latest()
            ->first();

        if ($latestSafetyStock) {
            // Update safety stock dan period dengan data terbaru
            $reorderPoint->safety_stock = $latestSafetyStock->hasil;
            $reorderPoint->period = $latestSafetyStock->bulan;
        }

        // Hitung permintaan harian (d/hari kerja)
        $permintaanHarian = $reorderPoint->permintaan_per_periode / $reorderPoint->total_hari_kerja;

        // Hitung ulang reorder point: ROP = SS + (LT × (d/hari kerja))
        $hasil = $reorderPoint->safety_stock + ($reorderPoint->lead_time * $permintaanHarian);

        $reorderPoint->hasil = $hasil;
        $reorderPoint->save();

        $message = $latestSafetyStock
            ? 'Safety Stock berhasil diperbarui dan Reorder Point berhasil dihitung ulang.'
            : 'Reorder Point berhasil dihitung ulang (Safety Stock tidak ditemukan).';

        return redirect()->route('reorder-point.index')
            ->with('success', $message);
    }

    public function edit($id)
    {
        $reorderPoint = ReorderPoint::with('barang')->findOrFail($id);
        return view('reorder_point.edit', compact('reorderPoint'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lead_time' => 'required|numeric|min:0',
            'permintaan_per_periode' => 'required|numeric|min:0',
            'total_hari_kerja' => 'required|numeric|min:1',
        ]);

        $reorderPoint = ReorderPoint::findOrFail($id);

        // Update data
        $reorderPoint->lead_time = $request->lead_time;
        $reorderPoint->permintaan_per_periode = $request->permintaan_per_periode;
        $reorderPoint->total_hari_kerja = $request->total_hari_kerja;

        // Hitung ulang ROP
        $permintaanHarian = $request->permintaan_per_periode / $request->total_hari_kerja;
        $reorderPoint->hasil = $reorderPoint->safety_stock + ($request->lead_time * $permintaanHarian);

        $reorderPoint->save();

        return redirect()->route('reorder-point.index')
            ->with('success', 'Reorder Point berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $reorderPoint = ReorderPoint::findOrFail($id);
            $reorderPoint->delete();

            return redirect()->route('reorder-point.index')
                ->with('success', 'Reorder Point berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('reorder-point.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}
