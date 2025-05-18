@extends('layouts.app')

@section('title', 'Detail Supplier')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Detail Supplier</h6>
                <div>
                    <a href="{{ route('supplier.edit', $supplier->id) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('supplier.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="30%">Nama Supplier</th>
                                <td>{{ $supplier->nama }}</td>
                            </tr>
                            <tr>
                                <th>Nama Kontak</th>
                                <td>{{ $supplier->kontak }}</td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td>{{ $supplier->alamat }}</td>
                            </tr>
                            <tr>
                                <th>Kota</th>
                                <td>{{ $supplier->kota }}</td>
                            </tr>
                            <tr>
                                <th>Provinsi</th>
                                <td>{{ $supplier->provinsi }}</td>
                            </tr>
                            <tr>
                                <th>Telepon 1</th>
                                <td>{{ $supplier->telp_1 }}</td>
                            </tr>
                            <tr>
                                <th>Telepon 2</th>
                                <td>{{ $supplier->telp_2 ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $supplier->email ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <form action="{{ route('supplier.destroy', $supplier->id) }}" method="POST" class="mt-3"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Hapus Supplier
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
