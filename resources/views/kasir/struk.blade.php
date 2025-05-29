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

        .whatsapp-section {
            background: #e8f5e8;
            border: 2px solid #28a745;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }

        .floating-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }

        .loading-spinner {
            display: none;
            margin-left: 10px;
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
            .no-print,
            .whatsapp-section {
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
    <!-- Floating Alerts -->
    <div id="alertContainer"></div>

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

            <!-- WhatsApp Section -->
            <div class="whatsapp-section no-print">
                <h5 class="mb-3">
                    <i class="fab fa-whatsapp text-success"></i> Kirim Struk via WhatsApp
                </h5>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="no_whatsapp" class="form-label">
                            <i class="fab fa-whatsapp"></i> Nomor WhatsApp:
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-success text-white">
                                <i class="fab fa-whatsapp"></i>
                            </span>
                            <input type="text" class="form-control" id="no_whatsapp"
                                value="{{ $checkout['no_whatsapp'] ?? '' }}" placeholder="08123456789">
                        </div>
                        <small class="text-muted">Format: 08123456789 (tanpa +62)</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-success w-100 btn-action" id="btnKirimWA"
                            onclick="kirimStrukWhatsApp()">
                            <i class="fab fa-whatsapp"></i> Kirim Struk
                            <div class="loading-spinner spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </button>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Fungsi untuk menampilkan alert
        function showAlert(type, message) {
            const alertContainer = document.getElementById('alertContainer');
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show floating-alert`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            alertContainer.appendChild(alertDiv);

            // Auto hide after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Fungsi untuk mengirim struk via WhatsApp - DIPERBAIKI
        function kirimStrukWhatsApp() {
            const noWa = document.getElementById('no_whatsapp').value.trim();
            const btnKirim = document.getElementById('btnKirimWA');
            const spinner = btnKirim.querySelector('.loading-spinner');

            if (!noWa) {
                showAlert('danger', 'Nomor WhatsApp belum diisi!');
                return;
            }

            // Validasi format nomor WhatsApp
            if (!/^(08|62)\d{8,13}$/.test(noWa)) {
                showAlert('danger', 'Format nomor WhatsApp tidak valid! Gunakan format: 08123456789');
                return;
            }

            // Tampilkan loading
            btnKirim.disabled = true;
            spinner.style.display = 'inline-block';

            // Data checkout dari PHP
            const checkout = @json($checkout);

            // Kirim request ke server
            fetch("{{ route('kirim.wa') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        no_whatsapp: noWa,
                        nama_pelanggan: checkout.nama_pelanggan,
                        total: checkout.total,
                        tunai: checkout.tunai,
                        kembalian: checkout.kembalian,
                        invoice_number: checkout.invoice_number,
                        items: checkout.items
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Sembunyikan loading
                    btnKirim.disabled = false;
                    spinner.style.display = 'none';

                    if (data.status === 'success') {
                        showAlert('success', data.message);
                        // Simpan nomor WhatsApp untuk penggunaan selanjutnya
                        if (typeof(Storage) !== "undefined") {
                            localStorage.setItem('last_whatsapp', noWa);
                        }
                    } else {
                        showAlert('danger', 'Gagal mengirim struk: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Sembunyikan loading
                    btnKirim.disabled = false;
                    spinner.style.display = 'none';
                    showAlert('danger', 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.');
                });
        }

        // Auto load last WhatsApp number
        window.addEventListener('load', function() {
            if (typeof(Storage) !== "undefined") {
                const lastWa = localStorage.getItem('last_whatsapp');
                const waInput = document.getElementById('no_whatsapp');
                if (lastWa && !waInput.value) {
                    waInput.value = lastWa;
                }
            }
        });

        // Format nomor WhatsApp saat user mengetik
        document.getElementById('no_whatsapp').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, ''); // Remove non-digits

            // Convert 62 prefix to 08
            if (value.startsWith('62')) {
                value = '0' + value.substring(2);
            }

            this.value = value;
        });

        // Enter key untuk kirim WhatsApp
        document.getElementById('no_whatsapp').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                kirimStrukWhatsApp();
            }
        });

        // Auto dismiss alert when clicked
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-close')) {
                const alert = e.target.closest('.alert');
                if (alert) {
                    alert.remove();
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
