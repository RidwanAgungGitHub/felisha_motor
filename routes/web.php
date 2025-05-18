<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\SafetyStockController;
use App\Http\Controllers\ReorderPointController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\InventoriManagementController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('barang', BarangController::class);
    Route::resource('barang_masuk', BarangMasukController::class)->except(['create']);
    Route::get('barang_masuk/create/{id?}', [BarangMasukController::class, 'create'])->name('barang_masuk.create.from.barang');
    Route::put('/barang-masuk/{barangMasuk}/terima', [BarangMasukController::class, 'terima'])->name('barang_masuk.terima');

    Route::resource('supplier', SupplierController::class);
    Route::get('/api/supplier', function () {
        return response()->json(App\Models\Supplier::orderBy('nama')->get());
    })->name('api.supplier');


    // Barang Keluar Routes
    Route::get('barang-keluar', [BarangKeluarController::class, 'index'])->name('barang-keluar.index');
    Route::get('barang-keluar/create', [BarangKeluarController::class, 'create'])->name('barang-keluar.create');
    Route::post('barang-keluar', [BarangKeluarController::class, 'store'])->name('barang-keluar.store');
    Route::get('barang-keluar/{barangKeluar}', [BarangKeluarController::class, 'show'])->name('barang-keluar.show');

    // Kasir Routes Tanpa JavaScript
    Route::get('kasir', [BarangKeluarController::class, 'kasir'])->name('kasir');
    Route::post('kasir/add-to-cart', [BarangKeluarController::class, 'addToCart'])->name('kasir.add-to-cart');
    Route::post('kasir/update-cart-qty', [BarangKeluarController::class, 'updateCartQty'])->name('kasir.update-cart-qty');
    Route::post('kasir/remove-from-cart', [BarangKeluarController::class, 'removeFromCart'])->name('kasir.remove-from-cart');
    Route::post('kasir/clear-cart', [BarangKeluarController::class, 'clearCart'])->name('kasir.clear-cart');
    Route::post('kasir/checkout', [BarangKeluarController::class, 'checkout'])->name('kasir.checkout');
    Route::get('kasir/struk', [BarangKeluarController::class, 'struk'])->name('kasir.struk');
    Route::get('kasir/hitung-kembalian', [BarangKeluarController::class, 'hitungKembalian'])->name('kasir.hitung-kembalian');
    // Route lama yang mungkin masih dibutuhkan
    Route::get('get-barang', [App\Http\Controllers\BarangKeluarController::class, 'getBarang'])->name('get-barang');


    // Safety Stock Routes
    Route::get('/safety-stock', [SafetyStockController::class, 'index'])->name('safety-stock.index');
    Route::get('/safety-stock/create', [SafetyStockController::class, 'create'])->name('safety-stock.create');
    Route::post('/safety-stock', [SafetyStockController::class, 'store'])->name('safety-stock.store');
    Route::get('/safety-stock/get-barang-data', [SafetyStockController::class, 'getBarangData'])->name('safety-stock.get-barang-data');
    Route::post('/safety-stock/{id}/calculate', [SafetyStockController::class, 'calculate'])->name('safety-stock.calculate');
    Route::get('/safety-stock/{id}/edit', [SafetyStockController::class, 'edit'])->name('safety-stock.edit');
    Route::put('/safety-stock/{id}', [SafetyStockController::class, 'update'])->name('safety-stock.update');

    // Reorder Point Routes
    Route::get('/reorder-point', [ReorderPointController::class, 'index'])->name('reorder-point.index');
    Route::get('/reorder-point/create', [ReorderPointController::class, 'create'])->name('reorder-point.create');
    Route::post('/reorder-point', [ReorderPointController::class, 'store'])->name('reorder-point.store');
    Route::get('/reorder-point/get-safety-stock', [ReorderPointController::class, 'getSafetyStock'])->name('reorder-point.get-safety-stock');
    Route::post('/reorder-point/{id}/calculate', [ReorderPointController::class, 'calculate'])->name('reorder-point.calculate');


    // Inventory Management unified view
    Route::get('/inventori-management', [InventoriManagementController::class, 'index'])->name('inventori-management.index');

    // Reports
    Route::get('/reports/inventory-status', [ReportController::class, 'inventoryStatus'])->name('reports.inventory-status');
    Route::get('/reports/inventory-status/export', [ReportController::class, 'exportInventoryStatus'])->name('reports.inventory-status.export');
});
