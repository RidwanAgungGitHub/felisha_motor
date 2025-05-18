<!-- resources/views/safety_stock/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Data Safety Stock</h5>
                        <a href="{{ route('safety-stock.create') }}" class="btn btn-primary">Tambah Safety Stock</a>
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
                                            <td>{{ number_format($ss->pemakaian_maksimum, 0) }} {{ $ss->barang->satuan }}
                                            </td>
                                            <td>{{ number_format($ss->pemakaian_rata_rata, 0) }} {{ $ss->barang->satuan }}
                                            </td>
                                            <td>{{ $ss->lead_time }} hari</td>
                                            <td>{{ number_format($ss->hasil, 0) }} {{ $ss->barang->satuan }}</td>
                                            <td>
                                                <form action="{{ route('safety-stock.calculate', $ss->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-info">Hitung Ulang</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data</td>
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
