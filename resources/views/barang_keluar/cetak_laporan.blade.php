<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Barang Keluar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
        }
        .info {
            margin-bottom: 20px;
        }
        .info table {
            width: 100%;
        }
        .info table td {
            padding: 5px;
            vertical-align: top;
        }
        .summary {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        .summary-box {
            border: 1px solid #ccc;
            padding: 10px;
            width: 30%;
            text-align: center;
        }
        .summary-box h3 {
            margin-top: 0;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data th, table.data td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table.data th {
            background-color: #f2f2f2;
        }
        .total-row {
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
        }
        @media print {
            body {
                padding: 0;
                font-size: 12pt;
            }
            button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" style="padding: 5px 10px; margin-bottom: 20px;">Cetak</button>

    <div class="header">
        <h1>LAPORAN BARANG KELUAR</h1>
        <p>{{ config('app.name', 'Laravel') }}</p>
        <p>Periode: {{ \Carbon\Carbon::parse($laporan['tanggal_mulai'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($laporan['tanggal_akhir'])->format('d/m/Y') }}</p>
    </div>

    <div class="summary">
        <div class="summary-box">
            <h3>Total Transaksi</h3>
            <p style="font-size: 1.5em;">{{ $laporan['jumlah_transaksi'] }}</p>
        </div>
        <div class="summary-box">
            <h3>Total Pendapatan</h3>
            <p style="font-size: 1.5em;">Rp {{ number_format($laporan['total_pendapatan'], 0, ',', '.') }}</p>
        </div>
        <div class="summary-box">
            <h3>Jumlah Barang Terjual</h3>
            <p style="font-size: 1.5em;">{{ $laporan['jumlah_barang'] }}</p>
        </div>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>No</th>
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
                    <td colspan="7" style="text-align: center;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: right;">Total</td>
                <td>Rp {{ number_format($laporan['total_pendapatan'], 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        <div style="margin-top: 60px;">
            <p>( __________________________ )</p>
            <p>Manager</p>
        </div>
    </div>
</body>
</html>