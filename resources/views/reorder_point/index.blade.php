<!-- resources/views/reorder_point/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Data Reorder Point</h5>
                        <a href="{{ route('reorder-point.create') }}" class="btn btn-primary">Tambah Reorder Point</a>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Safety Stock</th>
                                        <th>Lead Time</th>
                                        <th>Permintaan/Periode</th>
                                        <th>Hari Kerja/Periode</th>
                                        <th>Hasil</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reorderPoints as $index => $rop)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $rop->barang->nama_barang }} - {{ $rop->barang->merek }}</td>
                                            <td>{{ number_format($rop->safety_stock, 0) }} {{ $rop->barang->satuan }}</td>
                                            <td>{{ $rop->lead_time }} hari</td>
                                            <td>{{ number_format($rop->permintaan_per_periode, 0) }}
                                                {{ $rop->barang->satuan }}</td>
                                            <td>{{ $rop->total_hari_kerja }} hari</td>
                                            <td>{{ number_format($rop->hasil, 0) }} {{ $rop->barang->satuan }}</td>
                                            <td>
                                                <a href="{{ route('reorder-point.edit', $rop->id) }}"
                                                    class="btn btn-sm btn-warning">Edit</a>
                                                <form action="{{ route('reorder-point.calculate', $rop->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-info">Hitung Ulang</button>
                                                </form>
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
