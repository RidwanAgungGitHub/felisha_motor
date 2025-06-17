<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\SafetyStockController;
use App\Http\Controllers\ReorderPointController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\WhatsAppController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes (tidak perlu middleware)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route khusus untuk landing setelah login - TANPA MIDDLEWARE DULU
Route::get('/admin-dashboard', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    if ($user->role !== 'admin') {
        return redirect('/kasir-dashboard');
    }

    // Redirect ke dashboard admin yang sebenarnya
    return redirect('/dashboard');
})->name('admin-landing');

Route::get('/kasir-dashboard', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    if ($user->role !== 'kasir') {
        return redirect('/admin-dashboard');
    }

    // Redirect ke kasir yang sebenarnya
    return redirect('/kasir');
})->name('kasir-landing');

// Routes khusus untuk Role ADMIN
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard - hanya admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Barang Management - Full CRUD
    Route::resource('barang', BarangController::class);

    // Barang Masuk
    Route::resource('barang_masuk', BarangMasukController::class)->except(['create']);
    Route::get('barang_masuk/create/{id?}', [BarangMasukController::class, 'create'])->name('barang_masuk.create.from.barang');
    Route::put('/barang-masuk/{barangMasuk}/terima', [BarangMasukController::class, 'terima'])->name('barang_masuk.terima');

    // Supplier Management
    Route::resource('supplier', SupplierController::class);
    Route::get('/api/supplier', function () {
        return response()->json(App\Models\Supplier::orderBy('nama')->get());
    })->name('api.supplier');

    // Barang Keluar
    Route::get('/barang-keluar', [BarangKeluarController::class, 'index'])->name('barang-keluar.index');
    Route::post('/barang-keluar', [BarangKeluarController::class, 'store'])->name('barang-keluar.store');
    Route::get('/barang-keluar/{barangKeluar}', [BarangKeluarController::class, 'show'])->name('barang-keluar.show');
    Route::get('/barang-keluar/get-barang', [BarangKeluarController::class, 'getBarang'])->name('barang-keluar.get-barang');
    Route::get('/barang-keluar/laporan', [BarangKeluarController::class, 'laporan'])->name('barang-keluar.laporan');
    Route::get('/barang-keluar/export', [BarangKeluarController::class, 'export'])->name('barang-keluar.export');
    Route::get('/download-template', [TemplateController::class, 'downloadTemplate'])->name('download.template');
    Route::post('/barang-keluar/import', [BarangKeluarController::class, 'import'])->name('barang-keluar.import');

    // Safety Stock Management
    Route::get('/safety-stock', [SafetyStockController::class, 'index'])->name('safety-stock.index');
    Route::get('/safety-stock/create', [SafetyStockController::class, 'create'])->name('safety-stock.create');
    Route::post('/safety-stock', [SafetyStockController::class, 'store'])->name('safety-stock.store');
    Route::get('/safety-stock/get-barang-data', [SafetyStockController::class, 'getBarangData'])->name('safety-stock.get-barang-data');
    Route::get('/safety-stock/{id}/edit', [SafetyStockController::class, 'edit'])->name('safety-stock.edit');
    Route::put('/safety-stock/{id}', [SafetyStockController::class, 'update'])->name('safety-stock.update');
    Route::post('/safety-stock/{id}/recalculate', [SafetyStockController::class, 'recalculate'])->name('safety-stock.recalculate');
    Route::delete('/safety-stock/{id}', [SafetyStockController::class, 'destroy'])->name('safety-stock.destroy');

    // Reorder Point Management
    Route::get('/reorder-point', [ReorderPointController::class, 'index'])->name('reorder-point.index');
    Route::get('/reorder-point/create', [ReorderPointController::class, 'create'])->name('reorder-point.create');
    Route::post('/reorder-point', [ReorderPointController::class, 'store'])->name('reorder-point.store');
    Route::get('/reorder-point/{id}/edit', [ReorderPointController::class, 'edit'])->name('reorder-point.edit');
    Route::put('/reorder-point/{id}', [ReorderPointController::class, 'update'])->name('reorder-point.update');
    Route::get('/reorder-point/get-safety-stock', [ReorderPointController::class, 'getSafetyStock'])->name('reorder-point.get-safety-stock');
    Route::post('/reorder-point/{id}/recalculate', [ReorderPointController::class, 'recalculate'])->name('reorder-point.recalculate');
    Route::delete('/reorder-point/{id}', [ReorderPointController::class, 'destroy'])->name('reorder-point.destroy');

    // Reports Management
    Route::get('/reports/inventory-status', [ReportController::class, 'inventoryStatus'])->name('reports.inventory-status');
    Route::get('/reports/inventory-status/export', [ReportController::class, 'exportInventoryStatus'])->name('reports.inventory-status.export');
    Route::get('/reports/inventory-status/print', [ReportController::class, 'printInventoryStatus'])->name('reports.inventory-status.print');
});

// Routes khusus untuk Role KASIR
Route::middleware(['auth', 'role:kasir'])->group(function () {
    // Kasir Routes
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir');
    Route::post('/kasir/add-to-cart', [KasirController::class, 'addToCart'])->name('kasir.add-to-cart');
    Route::post('/kasir/update-cart-qty', [KasirController::class, 'updateCartQty'])->name('kasir.update-cart-qty');
    Route::post('/kasir/remove-from-cart', [KasirController::class, 'removeFromCart'])->name('kasir.remove-from-cart');
    Route::post('/kasir/clear-cart', [KasirController::class, 'clearCart'])->name('kasir.clear-cart');
    Route::post('/kasir/checkout', [KasirController::class, 'checkout'])->name('kasir.checkout');
    Route::get('/kasir/struk', [KasirController::class, 'struk'])->name('kasir.struk');
    Route::get('/kasir/hitung-kembalian', [KasirController::class, 'hitungKembalian'])->name('kasir.hitung-kembalian');
    Route::get('/kasir/cetak-laporan', [KasirController::class, 'cetakLaporan'])->name('kasir.cetak-laporan');
    Route::post('/kirim-wa', [KasirController::class, 'kirimPesanWhatsapp'])->name('kirim.wa');
    Route::get('/kasir/logout', function () {
        Auth::logout();
        return redirect('/login')->with('success', 'Anda berhasil logout.');
    })->name('kasir.logout');
});

// Fallback route untuk root
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect('/dashboard');
        } else {
            return redirect('/kasir');
        }
    }
    return redirect('/login');
});


Route::post('/dashboard/send-whatsapp', [DashboardController::class, 'sendWhatsAppToSupplier'])
    ->name('dashboard.send-whatsapp');
