<!-- resources/views/safety_stock/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Tambah Safety Stock</h5>
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
                        <form action="{{ route('safety-stock.create') }}" method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="barang_id">Nama Barang</label>
                                        <select class="form-control" id="barang_id" name="barang_id" required>
                                            <option value="">-- Pilih Barang --</option>
                                            @foreach ($barangs as $barang)
                                                <option value="{{ $barang->id }}"
                                                    {{ $selectedBarangId == $barang->id ? 'selected' : '' }}>
                                                    {{ $barang->nama_barang }} - {{ $barang->merek }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="bulan">Bulan</label>
                                                <select class="form-control" id="bulan" name="bulan">
                                                    @foreach ($availableMonths as $monthValue)
                                                        <option value="{{ $monthValue }}"
                                                            {{ $bulan == $monthValue ? 'selected' : '' }}>
                                                            {{ date('F', mktime(0, 0, 0, $monthValue, 1)) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="tahun">Tahun</label>
                                                <select class="form-control" id="tahun" name="tahun">
                                                    @foreach ($availableYears as $yearValue)
                                                        <option value="{{ $yearValue }}"
                                                            {{ $tahun == $yearValue ? 'selected' : '' }}>
                                                            {{ $yearValue }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-secondary">Pilih Barang</button>
                        </form>

                        <!-- Form untuk menambah safety stock -->
                        @if ($selectedBarangId)
                            <form action="{{ route('safety-stock.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="barang_id" value="{{ $selectedBarangId }}">
                                <input type="hidden" name="bulan" value="{{ $bulanTahun ?? now()->format('m/Y') }}">

                                <div class="form-group mb-3">
                                    <label for="periode">Periode</label>
                                    <input type="text" class="form-control" id="periode"
                                        value="{{ $bulanTahun ?? now()->format('m/Y') }}" readonly>
                                    <small class="text-muted">Format: Bulan/Tahun</small>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="pemakaian_maksimum">Pemakaian Maksimum</label>
                                    <input type="number" class="form-control" id="pemakaian_maksimum"
                                        name="pemakaian_maksimum" value="{{ $pemakaianMaksimum }}" required readonly>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="pemakaian_rata_rata">Pemakaian rata-rata perhari</label>
                                    <input type="number" class="form-control" id="pemakaian_rata_rata"
                                        name="pemakaian_rata_rata" value="{{ $pemakaianRataRata }}" required readonly>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="lead_time">Lead Time (hari)</label>
                                    <input type="number" class="form-control" id="lead_time" name="lead_time"
                                        value="{{ old('lead_time') }}" required>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('safety-stock.index') }}" class="btn btn-secondary">Kembali</a>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-info">
                                Pilih barang terlebih dahulu untuk melihat data pemakaian.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
