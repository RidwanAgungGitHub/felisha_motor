<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0 10px 0;
        }

        .report-period {
            font-size: 14px;
            margin-bottom: 20px;
        }

        .summary {
            background-color: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        .summary-item {
            display: inline-block;
            width: 30%;
            margin-right: 3%;
            text-align: center;
        }

        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        .summary-label {
            font-size: 11px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }

        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="report-title">LAPORAN PENJUALAN</div>
    <div class="report-period">
        Periode: {{ date('d/m/Y', strtotime($laporan['tanggal_mulai'])) }} -
        {{ date('d/m/Y', strtotime($laporan['tanggal_akhir'])) }}
    </div>

    <!-- Ringkasan Laporan -->
    <div class="summary">
        <div class="summary-item">
            <div class="summary-value">{{ $laporan['jumlah_transaksi'] }}</div>
            <div class="summary-label">Total Transaksi</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">{{ $laporan['jumlah_barang'] }}</div>
            <div class="summary-label">Total Barang Terjual</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">Rp {{ number_format($laporan['total_pendapatan'], 0, ',', '.') }}</div>
            <div class="summary-label">Total Pendapatan</div>
        </div>
    </div>

    <!-- Tabel Detail Transaksi -->
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="25%">Nama Barang</th>
                <th width="15%">Merek</th>
                <th width="10%">Jumlah</th>
                <th width="15%">Harga Satuan</th>
                <th width="15%">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $grandTotal = 0;
            @endphp
            @forelse($data as $item)
                @php
                    $grandTotal += $item->total_harga;
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $item->barang->nama_barang }}</td>
                    <td>{{ $item->barang->merek }}</td>
                    <td class="text-center">{{ $item->jumlah }} {{ $item->barang->satuan }}</td>
                    <td class="text-right">Rp {{ number_format($item->barang->harga, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data transaksi untuk periode ini</td>
                </tr>
            @endforelse

            @if ($data->count() > 0)
                <tr class="total-row">
                    <td colspan="6" class="text-right"><strong>GRAND TOTAL:</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
        <p>Kasir: {{ Auth::user()->name ?? 'Admin' }}</p>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html> class="header">
<div class="company-name">{{ config('app.name', 'Nama Toko') }}</div>
<div>Jl. Contoh No. 123, Kota</div>
<div>Telp: (021) 1234567</div>
</div>

<div
