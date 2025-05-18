@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Laporan Barang Keluar</h5>
                    <div>
                        <form action="{{ route('barang-keluar.laporan') }}" method="GET" class="form-inline">
                            <div class="input-group mr-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Dari</span>
                                </div>
                                <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai', date('Y-m-01')) }}">
                            </div>
                            <div class="input-group mr-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Sampai</span>
                                </div>
                                <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir', date('Y-m-d')) }}">
                            </div>
                            <button type="submit" class="btn btn-light">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Ringkasan Laporan -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Transaksi</h5>
                                    <h2 class="card-text">{{ $laporan['jumlah_transaksi'] }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Pendapatan</h5>
                                    <h2 class="card-text">Rp {{ number_format($laporan['total_pendapatan'], 0, ',', '.') }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Jumlah Barang Terjual</h5>
                                    <h2 class="card-text">{{ $laporan['jumlah_barang'] }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Cetak -->
                    <div class="mb-3">
                        <a href="{{ route('barang-keluar.laporan.cetak') }}?tanggal_mulai={{ request('tanggal_mulai', date('Y-m-01')) }}&tanggal_akhir={{ request('tanggal_akhir', date('Y-m-d')) }}" class="btn btn-success" target="_blank">
                            <i class="fas fa-print"></i> Cetak Laporan
                        </a>
                    </div>

                    <!-- Tabel Data Barang Keluar -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th>Total Harga</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                        <td>{{ $item->barang->nama_barang }} - {{ $item->barang->merek }}</td>
                                        <td>{{ $item->jumlah }} {{ $item->barang->satuan }}</td>
                                        <td>Rp {{ number_format($item->barang->harga, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="5" class="text-right">Total</td>
                                    <td>Rp {{ number_format($laporan['total_pendapatan'], 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-center">
                        {{ $data->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection