<!-- resources/views/reorder_point/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Tambah Reorder Point</h5>
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

                        <!-- Form untuk memilih barang -->
                        <form action="{{ route('reorder-point.create') }}" method="GET" class="mb-4">
                            <div class="form-group">
                                <label for="barang_id">Nama Barang</label>
                                <select class="form-control select2" id="barang_id" name="barang_id" required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach ($barangs as $barang)
                                        <option value="{{ $barang->id }}"
                                            {{ $selectedBarangId == $barang->id ? 'selected' : '' }}>
                                            {{ $barang->nama_barang }} - {{ $barang->merek }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-secondary">Pilih Barang</button>
                        </form>

                        <!-- Form untuk menambah reorder point -->
                        @if ($selectedBarangId)
                            <form action="{{ route('reorder-point.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="barang_id" value="{{ $selectedBarangId }}">

                                <div class="form-group mb-3">
                                    <label for="safety_stock">Safety Stock</label>
                                    <input type="number" class="form-control" id="safety_stock" name="safety_stock"
                                        value="{{ $safetyStock }}" required readonly>
                                    <small class="form-text text-muted">Nilai Safety Stock dari perhitungan terbaru</small>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="period">Period</label>
                                    <input type="text" class="form-control" id="period" name="period"
                                        value="{{ $period }}" required readonly>
                                    <small class="form-text text-muted">Period dari Safety Stock</small>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="lead_time">Lead Time (hari)</label>
                                    <input type="number" class="form-control" id="lead_time" name="lead_time"
                                        value="{{ old('lead_time') }}" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="permintaan_per_periode">Permintaan Per Periode</label>
                                    <input type="number" class="form-control" id="permintaan_per_periode"
                                        name="permintaan_per_periode" value="{{ old('permintaan_per_periode') }}" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="total_hari_kerja">Total Hari Kerja Per Periode</label>
                                    <input type="number" class="form-control" id="total_hari_kerja" name="total_hari_kerja"
                                        value="{{ old('total_hari_kerja') }}" required>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('reorder-point.index') }}" class="btn btn-secondary">Kembali</a>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-info">
                                Pilih barang terlebih dahulu untuk melihat data safety stock.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize select2
            $('.select2').select2({
                placeholder: "-- Pilih Barang --",
                width: '100%'
            });
        });
    </script>
@endpush
