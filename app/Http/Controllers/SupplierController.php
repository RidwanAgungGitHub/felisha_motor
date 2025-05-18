<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(10);
        return view('supplier.index', compact('suppliers'));
    }

    public function create()
    {
        return view('supplier.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kontak' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'telp_1' => 'required|string|max:20',
            'telp_2' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        Supplier::create($validated);

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil ditambahkan!');
    }

    public function show(Supplier $supplier)
    {
        return view('supplier.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('supplier.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kontak' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'telp_1' => 'required|string|max:20',
            'telp_2' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $supplier->update($validated);

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil diperbarui!');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil dihapus!');
    }
}
