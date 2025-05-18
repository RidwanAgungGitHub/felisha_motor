<!-- resources/views/reports/print/inventory_status.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Status Inventori</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .report-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
        }

        .report-title {
            font-size: 18px;
        }

        .report-date {
            margin-top: 5px;
            font-style: italic;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        .status-normal {
            color: green;
            font-weight: bold;
        }

        .status-reorder {
            color: orange;
            font-weight: bold;
        }

        .status-safety {
            color: red;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
        }

        @media print {
            @page {
                size: landscape;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="report-header">
        <div class="company-name">{{ config('app.name', 'Laravel') }}</div>
        <div class="report-title">LAPORAN STATUS INVENTORI</div>
        <div class="report-date">Tanggal Cetak: {{ date('d-m-Y H:i:s') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Merek</th>
                <th>Stok Saat Ini</th>
                <th>Safety Stock</th>
                <th>Reorder Point</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($inventoryStatus as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['nama_barang'] }}</td>
                    <td>{{ $item['merek'] }}</td>
                    <td>{{ $item['stok'] }} {{ $item['satuan'] }}</td>
                    <td>{{ $item['safety_stock'] ? number_format($item['safety_stock'], 0) . ' ' . $item['satuan'] : '-' }}
                    </td>
                    <td>{{ $item['reorder_point'] ? number_format($item['reorder_point'], 0) . ' ' . $item['satuan'] : '-' }}
                    </td>
                    <td
                        class="status-{{ strtolower($item['status']) === 'normal' ? 'normal' : (strtolower($item['status']) === 'reorder point' ? 'reorder' : 'safety') }}">
                        {{ $item['status'] }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ Auth::user()->name ?? 'User' }}</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print();" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">
            Cetak Sekarang
        </button>
        <button onclick="window.close();"
            style="padding: 10px 20px; font-size: 16px; cursor: pointer; margin-left: 10px;">
            Tutup
        </button>
    </div>

    <script>
        window.onload = function() {
            // Automatically open print dialog when page loads
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>

</html>
