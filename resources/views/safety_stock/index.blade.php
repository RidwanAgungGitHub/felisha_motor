<!-- resources/views/safety_stock/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Data Safety Stock</h5>
                        <a href="{{ route('safety-stock.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Tambah Safety Stock
                        </a>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Periode</th>
                                        <th>Pemakaian Maksimum</th>
                                        <th>Pemakaian rata-rata perhari</th>
                                        <th>Lead Time</th>
                                        <th>Hasil</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($safetyStocks as $index => $ss)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $ss->barang->nama_barang }} - {{ $ss->barang->merek }}</td>
                                            <td>{{ $ss->bulan }}</td>
                                            <td>{{ number_format($ss->pemakaian_maksimum, 0) }} {{ $ss->barang->satuan }}
                                            </td>
                                            <td>{{ number_format($ss->pemakaian_rata_rata, 0) }} {{ $ss->barang->satuan }}
                                            </td>
                                            <td>{{ $ss->lead_time }} hari</td>
                                            <td>{{ number_format($ss->hasil, 0) }} {{ $ss->barang->satuan }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('safety-stock.edit', $ss->id) }}"
                                                        class="btn btn-sm btn-warning me-1" title="Edit">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>
                                                    <form action="{{ route('safety-stock.recalculate', $ss->id) }}"
                                                        method="POST" class="d-inline me-1">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-info"
                                                            title="Refresh Data & Hitung Ulang">
                                                            <i class="bi bi-arrow-clockwise"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('safety-stock.destroy', $ss->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
