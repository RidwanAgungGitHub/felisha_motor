<!-- resources/views/reorder_point/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Data Reorder Point</h5>
                        <a href="{{ route('reorder-point.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Tambah Reorder Point
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
                                        <th>Safety Stock</th>
                                        <th>Period</th>
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
                                            <td>{{ $rop->period }}</td>
                                            <td>{{ $rop->lead_time }} hari</td>
                                            <td>{{ number_format($rop->permintaan_per_periode, 0) }}
                                                {{ $rop->barang->satuan }}</td>
                                            <td>{{ $rop->total_hari_kerja }} hari</td>
                                            <td>{{ number_format($rop->hasil, 0) }} {{ $rop->barang->satuan }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('reorder-point.edit', $rop->id) }}"
                                                        class="btn btn-sm btn-warning me-1" title="Edit">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>
                                                    <form action="{{ route('reorder-point.recalculate', $rop->id) }}"
                                                        method="POST" class="d-inline me-1">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-info"
                                                            title="Refresh Safety Stock & Hitung Ulang">
                                                            <i class="bi bi-arrow-clockwise"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('reorder-point.destroy', $rop->id) }}"
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
                                            <td colspan="9" class="text-center">Tidak ada data</td>
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
