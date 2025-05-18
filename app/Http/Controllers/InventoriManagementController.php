<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\SafetyStock;
use App\Models\ReorderPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoriManagementController extends Controller
{
    public function index(Request $request)
    {
        // Get active tab from request or default to 'safety-stock'
        $activeTab = $request->input('tab', 'safety-stock');

        // Get safety stocks data
        $safetyStocks = SafetyStock::with('barang')->latest()->get();

        // Get reorder points data
        $reorderPoints = ReorderPoint::with('barang')->latest()->get();

        return view('inventori_management', compact(
            'safetyStocks',
            'reorderPoints',
            'activeTab'
        ));
    }

    public function createSafetyStock()
    {
        $barangs = Barang::where('stok', '>', 0)->get();
        return view('safety_stock.create', compact('barangs'));
    }

    public function createReorderPoint()
    {
        $barangs = Barang::where('stok', '>', 0)->get();
        return view('reorder_point.create', compact('barangs'));
    }
}
