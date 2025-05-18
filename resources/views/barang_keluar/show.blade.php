@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Detail Barang Keluar</h5>
                    </div>

                    <div class="card-body">
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3">Informasi Barang</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nama Barang:</strong> {{ $barangKeluar->barang->nama_barang }}</p>
                                    <p><strong>Merek:</strong> {{ $barangKeluar->barang->merek }}</p>
                                    <p><strong>Harga Satuan:</strong> Rp
                                        {{ number_format($barangKeluar->barang->harga, 0, ',', '.') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Satuan:</strong> {{ $barangKeluar->barang->satuan }}</p>
                                    <p><strong>Stok Tersisa:</strong> {{ $barangKeluar->barang->stok }}
                                        {{ $barangKeluar->barang->satuan }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3">Detail Transaksi</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Jumlah Keluar:</strong> {{ $barangKeluar->jumlah }}
                                        {{ $barangKeluar->barang->satuan }}</p>
                                    <p><strong>Total Harga:</strong> Rp
                                        {{ number_format($barangKeluar->total_harga, 0, ',', '.') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Tanggal:</strong> {{ $barangKeluar->tanggal->format('d/m/Y') }}</p>
                                    <p><strong>Keterangan:</strong> {{ $barangKeluar->keterangan ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3">Log Transaksi</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Dibuat pada:</strong> {{ $barangKeluar->created_at->format('d/m/Y H:i:s') }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Diperbarui pada:</strong>
                                        {{ $barangKeluar->updated_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('barang-keluar.index') }}" class="btn btn-secondary">Kembali</a>
                            <div>
                                <button onclick="window.print()" class="btn btn-info"><i class="fas fa-print"></i>
                                    Cetak</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
