@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </div>

        <!-- Dashboard Cards -->
        <div class="row">
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card card-dashboard border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Barang</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBarang }} Jenis</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card card-dashboard border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Stok</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStok }} Unit</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card card-dashboard border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Stok Menipis</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lowStockItems }} Barang</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Products -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Barang Terbaru</h6>
                        <a href="{{ route('barang.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Merek</th>
                                        <th>Satuan/Harga</th>
                                        <th>Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentItems as $item)
                                        <tr>
                                            <td>{{ $item->nama_barang }}</td>
                                            <td>{{ $item->merek }}</td>
                                            <td>{{ $item->satuan }} / Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                            <td>
                                                @if ($item->stok < 10)
                                                    <span class="badge bg-danger">{{ $item->stok }}</span>
                                                @else
                                                    {{ $item->stok }}
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada data barang terbaru.</td>
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
