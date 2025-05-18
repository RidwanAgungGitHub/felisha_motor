@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Edit Supplier</h6>
                <a href="{{ route('supplier.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('supplier.update', $supplier->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama" class="form-label">Nama Supplier</label>
                            <input type="text" class="form-control" id="nama" name="nama"
                                value="{{ old('nama', $supplier->nama) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="kontak" class="form-label">Nama Kontak</label>
                            <input type="text" class="form-control" id="kontak" name="kontak"
                                value="{{ old('kontak', $supplier->kontak) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" required>{{ old('alamat', $supplier->alamat) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="kota" class="form-label">Kota</label>
                            <input type="text" class="form-control" id="kota" name="kota"
                                value="{{ old('kota', $supplier->kota) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="provinsi" class="form-label">Provinsi</label>
                            <input type="text" class="form-control" id="provinsi" name="provinsi"
                                value="{{ old('provinsi', $supplier->provinsi) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telp_1" class="form-label">Telepon 1</label>
                            <input type="text" class="form-control" id="telp_1" name="telp_1"
                                value="{{ old('telp_1', $supplier->telp_1) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telp_2" class="form-label">Telepon 2 (Opsional)</label>
                            <input type="text" class="form-control" id="telp_2" name="telp_2"
                                value="{{ old('telp_2', $supplier->telp_2) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email (Opsional)</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="{{ old('email', $supplier->email) }}">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
