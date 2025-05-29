<!-- resources/views/reorder_point/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Edit Reorder Point</h5>
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

                        <form action="{{ route('reorder-point.update', $reorderPoint->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group mb-3">
                                <label for="barang_id">Nama Barang</label>
                                <input type="text" class="form-control"
                                    value="{{ $reorderPoint->barang->nama_barang }} - {{ $reorderPoint->barang->merek }}"
                                    readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label for="safety_stock">Safety Stock</label>
                                <input type="number" class="form-control" id="safety_stock" name="safety_stock"
                                    value="{{ $reorderPoint->safety_stock }}" readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label for="period">Period</label>
                                <input type="text" class="form-control" id="period" name="period"
                                    value="{{ $reorderPoint->period }}" readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label for="lead_time">Lead Time (hari)</label>
                                <input type="number" class="form-control" id="lead_time" name="lead_time"
                                    value="{{ $reorderPoint->lead_time }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="permintaan_per_periode">Permintaan Per Periode</label>
                                <input type="number" class="form-control" id="permintaan_per_periode"
                                    name="permintaan_per_periode" value="{{ $reorderPoint->permintaan_per_periode }}"
                                    required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="total_hari_kerja">Total Hari Kerja Per Periode</label>
                                <input type="number" class="form-control" id="total_hari_kerja" name="total_hari_kerja"
                                    value="{{ $reorderPoint->total_hari_kerja }}" required>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('reorder-point.index') }}" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
