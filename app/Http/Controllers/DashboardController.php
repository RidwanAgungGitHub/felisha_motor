<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBarang = Barang::count();
        $totalStok = Barang::sum('stok');
        $lowStockItems = Barang::where('stok', '<', 10)->count();
        $recentItems = Barang::latest()->take(5)->get();

        return view('dashboard', compact('totalBarang', 'totalStok', 'lowStockItems', 'recentItems'));
    }
}
