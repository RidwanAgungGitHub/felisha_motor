<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\SafetyStock;
use App\Models\ReorderPoint;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Generate inventory status report
     */
    public function inventoryStatus(Request $request)
    {
        // Get all active products
        $barangs = Barang::orderBy('nama_barang')->get();

        $inventoryStatus = [];

        foreach ($barangs as $barang) {
            // Get latest safety stock and reorder point data for this product if exists
            $safetyStock = SafetyStock::where('barang_id', $barang->id)
                ->latest()
                ->first();

            $reorderPoint = ReorderPoint::where('barang_id', $barang->id)
                ->latest()
                ->first();

            // Calculate inventory status
            $status = 'Normal';
            $statusClass = 'success';

            if ($reorderPoint && $barang->stok <= $reorderPoint->hasil) {
                $status = 'Reorder Point';
                $statusClass = 'warning';
            }

            if ($safetyStock && $barang->stok <= $safetyStock->hasil) {
                $status = 'Safety Stock';
                $statusClass = 'danger';
            }

            // Add to inventory status array
            $inventoryStatus[] = [
                'id' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'merek' => $barang->merek,
                'stok' => $barang->stok,
                'satuan' => $barang->satuan,
                'safety_stock' => $safetyStock ? $safetyStock->hasil : null,
                'reorder_point' => $reorderPoint ? $reorderPoint->hasil : null,
                'status' => $status,
                'status_class' => $statusClass,
            ];
        }

        return view('reports.inventory_status', compact('inventoryStatus'));
    }
}
