<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - {{ $checkout['invoice_number'] }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .struk-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .struk-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .struk-body {
            padding: 30px;
        }

        .struk-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .struk-item:last-child {
            border-bottom: none;
        }

        .total-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .btn-action {
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            margin: 5px;
        }

        .company-info {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px dashed #ddd;
        }

        .transaction-info {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .struk-container {
                box-shadow: none;
                border-radius: 0;
            }

            .btn-action,
            .no-print {
                display: none !important;
            }

            .struk-header {
                background: #28a745 !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="struk-container">
        <div class="struk-header">
            <h2><i class="fas fa-receipt"></i> STRUK PEMBAYARAN</h2>
            <p class="mb-0">Falisa Inventory System</p>
        </div>

        <div class="struk-body">
            <!-- Company Info -->
            <div class="company-info">
                <h4>FALISA INVENTORY</h4>
                <p class="mb-1">Jl. Contoh Alamat No. 123</p>
                <p class="mb-1">Telp: (021) 1234567</p>
                <p class="mb-0">Email: info@falisainventory.com</p>
            </div>

            <!-- Transaction Info -->
            <div class="transaction-info">
                <div class="row">
                    <div class="col-6">
                        <strong>No. Invoice:</strong><br>
                        {{ $checkout['invoice_number'] }}
                    </div>
                    <div class="col-6 text-end">
                        <strong>Tanggal:</strong><br>
                        {{ date('d/m/Y H:i:s', strtotime($checkout['tanggal'])) }}
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-6">
                        <strong>Kasir:</strong><br>
                        {{ Auth::user()->name }}
                    </div>
                    <div class="col-6 text-end">
                        <strong>Pelanggan:</strong><br>
                        {{ $checkout['nama_pelanggan'] }}
                        @if ($checkout['no_whatsapp'])
                            <br><small>{{ $checkout['no_whatsapp'] }}</small>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items -->
            <h5 class="mb-3">Detail Pembelian:</h5>
            @foreach ($checkout['items'] as $item)
                <div class="struk-item">
                    <div class="row">
                        <div class="col-8">
                            <strong>{{ $item['nama_barang'] }}</strong>
                            <br><small class="text-muted">{{ $item['jumlah'] }} x Rp
                                {{ number_format($item['harga'], 0, ',', '.') }}</small>
                        </div>
                        <div class="col-4 text-end">
                            <strong>Rp {{ number_format($item['harga'] * $item['jumlah'], 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Total Section -->
            <div class="total-section">
                <div class="row mb-2">
                    <div class="col-6">
                        <strong>Subtotal:</strong>
                    </div>
                    <div class="col-6 text-end">
                        <strong>Rp {{ number_format($checkout['total'], 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-6">
                        <strong>Tunai:</strong>
                    </div>
                    <div class="col-6 text-end">
                        <strong>Rp {{ number_format($checkout['tunai'], 0, ',', '.') }}</strong>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <h5>Kembalian:</h5>
                    </div>
                    <div class="col-6 text-end">
                        <h5>Rp {{ number_format($checkout['kembalian'], 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-4">
                <p class="mb-1">Terima kasih atas kunjungan Anda!</p>
                <p class="mb-0 text-muted">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</p>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mt-4 no-print">
                <button onclick="window.print()" class="btn btn-primary btn-action">
                    <i class="fas fa-print"></i> Cetak Struk
                </button>
                <a href="{{ route('kasir') }}" class="btn btn-success btn-action">
                    <i class="fas fa-shopping-cart"></i> Transaksi Baru
                </a>
            </div>
        </div>
    </div>

    <script>
        // Auto focus untuk print
        window.addEventListener('load', function() {
            // Optional: Auto print setelah 2 detik
            // setTimeout(function() {
            //     window.print();
            // }, 2000);
        });

        // Clear session setelah print
        window.addEventListener('afterprint', function() {
            // Optional: Redirect ke kasir setelah print
            // window.location.href = "{{ route('kasir') }}";
        });
    </script>
</body>

</html>
