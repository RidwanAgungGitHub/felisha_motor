@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-receipt"></i> Struk Pembayaran</h5>
                        <button onclick="window.print()" class="btn btn-light btn-sm">
                            <i class="fas fa-print"></i> Cetak
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h4>STRUK PEMBAYARAN</h4>
                        <h5>{{ config('app.name', 'Laravel') }}</h5>
                        <p class="mb-0">Jl. Contoh No. 123, Kota</p>
                        <p>Telp: (021) 1234567</p>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>No. Invoice:</strong> {{ $checkout['invoice_number'] }}</p>
                            <p><strong>Tanggal: </strong> {{ $checkout['tanggal'] }}</p>
                        </div>
                        <div class="col-md-6 text-right">
                            <p><strong>Pelanggan:</strong> {{ $checkout['nama_pelanggan'] }}</p>
                            <p><strong>Nomor WhatsApp:</strong> {{ $checkout['no_whatsapp'] }}</p>
                            <p><strong>Kasir:</strong> {{ Auth::user()->name }}</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($checkout['items'] as $item)
                                @php
                                    $subtotal = $item['harga'] * $item['jumlah'];
                                @endphp
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item['nama_barang'] }}</td>
                                    <td>Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                                    <td>{{ $item['jumlah'] }}</td>
                                    <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td colspan="4" class="text-right">Total</td>
                                    <td>Rp {{ number_format($checkout['total'], 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right">Tunai</td>
                                    <td>Rp {{ number_format($checkout['tunai'], 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right">Kembalian</td>
                                    <td>Rp {{ number_format($checkout['kembalian'], 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="text-center mt-4">
                        <p>Terima kasih telah berbelanja di toko kami</p>
                        <p class="mb-0">Barang yang sudah dibeli tidak dapat ditukar kembali</p>
                    </div>

                    <!-- Tombol kembali ke halaman kasir -->
                    <div class="text-center mt-4">
                        <a href="{{ route('kasir') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Kasir
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print style yang hanya berlaku saat print -->
<style type="text/css" media="print">
    @page {
        size: 80mm auto;
        margin: 5mm;
    }

    body {
        font-size: 12pt;
    }

    .btn, .navbar, .card-header {
        display: none !important;
    }

    .card {
        border: none !important;
    }

    .card-body {
        padding: 0 !important;
    }

    .container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
</style>
@endsection