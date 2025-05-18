<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class BarangMasukController extends Controller
{
    public function index()
    {
        $barangMasuk = BarangMasuk::with(['barang', 'supplier'])->latest()->paginate(10);

        // Add these variables for the create modal
        $barangs = Barang::all();
        $suppliers = Supplier::all();
        $selectedBarang = null; // Add this line

        return view('barang_masuk.index', compact('barangMasuk', 'barangs', 'suppliers', 'selectedBarang'));
    }

    public function create($id = null)
    {
        $barangs = Barang::orderBy('nama_barang')->get();
        $suppliers = Supplier::orderBy('nama')->get();
        $selectedBarang = null;

        if ($id) {
            $selectedBarang = Barang::findOrFail($id);
        }

        return view('barang_masuk.create', compact('barangs', 'suppliers', 'selectedBarang'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'jumlah' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'tanggal_beli' => 'required|date',
            'estimasi_datang' => 'required|date',
            'status' => 'required|in:pending,diterima',
        ]);

        $barangMasuk = BarangMasuk::create($validated);

        // If status is 'diterima', update the stock
        if ($request->status === 'diterima') {
            $barang = Barang::find($request->barang_id);
            $barang->stok += $request->jumlah;
            $barang->save();
        }

        return redirect()->route('barang_masuk.index')
            ->with('success', 'Data barang masuk berhasil ditambahkan!');
    }

    public function update(Request $request, BarangMasuk $barangMasuk)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'jumlah' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'tanggal_beli' => 'required|date',
            'estimasi_datang' => 'required|date',
            'status' => 'required|in:pending,diterima',
        ]);

        // Simpan status lama untuk perbandingan
        $oldStatus = $barangMasuk->status;
        $oldJumlah = $barangMasuk->jumlah;

        // Update data barang masuk
        $barangMasuk->update($validated);

        // Jika status berubah dari pending ke diterima
        if ($oldStatus === 'pending' && $request->status === 'diterima') {
            // Update stok barang
            $barang = Barang::find($request->barang_id);
            $barang->stok += $request->jumlah;
            $barang->save();
        }
        // Jika status tetap diterima tapi jumlah berubah
        elseif ($oldStatus === 'diterima' && $request->status === 'diterima' && $oldJumlah != $request->jumlah) {
            // Update stok barang dengan selisih jumlah
            $barang = Barang::find($request->barang_id);
            $barang->stok = $barang->stok - $oldJumlah + $request->jumlah;
            $barang->save();
        }
        // Jika status berubah dari diterima ke pending
        elseif ($oldStatus === 'diterima' && $request->status === 'pending') {
            // Kurangi stok barang
            $barang = Barang::find($request->barang_id);
            $barang->stok -= $oldJumlah;
            $barang->save();
        }

        return redirect()->route('barang_masuk.index')
            ->with('success', 'Data barang masuk berhasil diperbarui!');
    }

    public function edit(BarangMasuk $barangMasuk)
    {
        $barangs = Barang::orderBy('nama_barang')->get();
        $suppliers = Supplier::orderBy('nama')->get();

        return view('barang_masuk.edit', compact('barangMasuk', 'barangs', 'suppliers'));
    }

    public function show(BarangMasuk $barangMasuk)
    {
        return view('barang_masuk.show', compact('barangMasuk'));
    }

    public function terima(BarangMasuk $barangMasuk)
    {
        // Only update if the status is pending
        if ($barangMasuk->status === 'pending') {
            $barangMasuk->status = 'diterima';
            $barangMasuk->save();

            // Update the stock
            $barang = $barangMasuk->barang;
            $barang->stok += $barangMasuk->jumlah;
            $barang->save();

            return redirect()->route('barang_masuk.show', $barangMasuk->id)
                ->with('success', 'Barang berhasil diterima dan stok telah diperbarui!');
        }

        return redirect()->route('barang_masuk.show', $barangMasuk->id)
            ->with('error', 'Barang sudah diterima sebelumnya!');
    }

    public function destroy(BarangMasuk $barangMasuk)
    {
        $barangMasuk->delete();

        return redirect()->route('barang_masuk.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}
