<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir - Falisa Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .kasir-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin: 20px;
            overflow: hidden;
        }

        .kasir-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            position: relative;
        }

        .kasir-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="20" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="60" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="70" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
        }

        .kasir-info {
            position: relative;
            z-index: 1;
        }

        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 2;
        }

        .product-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .cart-section {
            background: #f8f9fa;
            border-radius: 10px;
            min-height: 500px;
        }

        .payment-section {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 10px;
        }

        .btn-kasir {
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-kasir:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .cart-item {
            background: white;
            border-radius: 8px;
            margin-bottom: 10px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .total-display {
            font-size: 1.5rem;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }

        .floating-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }

        .select-barang {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 10px;
            font-size: 1rem;
        }

        .select-barang:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
    </style>
</head>

<body>
    <!-- Floating Alerts -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show floating-alert" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show floating-alert" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="kasir-container">
        <!-- Header Kasir -->
        <div class="kasir-header">
            <div class="kasir-info">
                <h2 class="mb-0">
                    <i class="fas fa-cash-register"></i>
                    Sistem Kasir Falisa Inventory
                </h2>
                <p class="mb-0 mt-2">
                    <i class="fas fa-user"></i> {{ Auth::user()->name }} -
                    <span class="badge bg-light text-dark">Kasir</span>
                    <span class="ms-3">
                        <i class="fas fa-calendar"></i> {{ date('d/m/Y H:i') }}
                    </span>
                </p>
            </div>

            <!-- Tombol Logout -->
            <div class="logout-btn">
                <button type="button" class="btn btn-outline-light btn-kasir" data-bs-toggle="modal"
                    data-bs-target="#logoutModal">
                    <i class="fas fa-power-off"></i> Keluar
                </button>
            </div>
        </div>

        <div class="p-4">
            <div class="row">
                <!-- Section Pilih Barang -->
                <div class="col-md-8">
                    <div class="product-card card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Pilih Barang</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('kasir.add-to-cart') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <label for="barang_id" class="form-label fw-bold">Pilih Barang:</label>
                                            <select id="barang_id" name="barang_id" class="form-select select-barang"
                                                required>
                                                <option value="">-- Pilih Barang --</option>
                                                @foreach ($data as $item)
                                                    <option value="{{ $item->id }}" data-harga="{{ $item->harga }}"
                                                        data-stok="{{ $item->stok }}">
                                                        {{ $item->nama_barang }} - {{ $item->merek }}
                                                        (Rp {{ number_format($item->harga, 0, ',', '.') }} | Stok:
                                                        {{ $item->stok }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="submit" class="btn btn-success btn-kasir w-100 btn-lg">
                                                <i class="fas fa-plus"></i> Tambah
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Keranjang Belanja -->
                    <div class="cart-section p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="fas fa-shopping-basket"></i> Keranjang Belanja</h5>
                            @if (session('cart') && count(session('cart')) > 0)
                                <button type="button" class="btn btn-outline-danger btn-sm btn-kasir clear-cart-btn">
                                    <i class="fas fa-trash"></i> Kosongkan
                                </button>
                            @endif
                        </div>

                        <div class="cart-items">
                            @if (session('cart') && count(session('cart')) > 0)
                                @php $total = 0; @endphp
                                @foreach (session('cart') as $id => $item)
                                    @php
                                        $subtotal = $item['harga'] * $item['jumlah'];
                                        $total += $subtotal;
                                    @endphp
                                    <div class="cart-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <h6 class="mb-1">{{ $item['nama_barang'] }}</h6>
                                                <small class="text-muted">{{ $item['merek'] ?? '' }}</small>
                                            </div>
                                            <div class="col-md-2">
                                                <span class="fw-bold">Rp
                                                    {{ number_format($item['harga'], 0, ',', '.') }}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <form action="{{ route('kasir.update-cart-qty') }}" method="POST"
                                                    class="d-flex">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $id }}">
                                                    <input type="number" name="jumlah"
                                                        value="{{ $item['jumlah'] }}" min="1"
                                                        max="{{ $item['stok_tersedia'] }}"
                                                        class="form-control form-control-sm me-2"
                                                        style="width: 80px;">
                                                    <button type="submit" class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-sync"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div class="col-md-2">
                                                <strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                                            </div>
                                            <div class="col-md-1">
                                                <form action="{{ route('kasir.remove-from-cart') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id"
                                                        value="{{ $id }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">Keranjang Kosong</h5>
                                    <p class="text-muted">Pilih barang untuk memulai transaksi</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Section Pembayaran -->
                <div class="col-md-4">
                    <div class="payment-section p-4">
                        <h5 class="mb-4"><i class="fas fa-credit-card"></i> Pembayaran</h5>

                        @if (session('cart') && count(session('cart')) > 0)
                            @php $total = 0; @endphp
                            @foreach (session('cart') as $item)
                                @php $total += $item['harga'] * $item['jumlah']; @endphp
                            @endforeach

                            <div class="total-display mb-4">
                                <div>Total Belanja</div>
                                <div>Rp {{ number_format($total, 0, ',', '.') }}</div>
                            </div>

                            <form action="{{ route('kasir.checkout') }}" method="POST" id="checkoutForm">
                                @csrf
                                <input type="hidden" name="total" id="total_checkout"
                                    value="{{ $total }}">

                                <div class="form-group mb-3">
                                    <label for="tunai" class="form-label fw-bold">Jumlah Tunai:</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">Rp</span>
                                        <input type="number" class="form-control form-control-lg" id="tunai"
                                            name="tunai" value="{{ $total }}" min="{{ $total }}"
                                            required>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="kembalian" class="form-label fw-bold">Kembalian:</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">Rp</span>
                                        <input type="text" class="form-control form-control-lg" id="kembalian"
                                            value="0" readonly>
                                    </div>
                                </div>
                                <hr class="my-4">

                                <div class="form-group mb-3">
                                    <label for="nama_pelanggan" class="form-label">Nama Pelanggan (Opsional):</label>
                                    <input type="text" class="form-control" id="nama_pelanggan"
                                        name="nama_pelanggan" placeholder="Masukkan nama pelanggan">
                                </div>

                                <div class="form-group mb-4">
                                    <label for="no_whatsapp" class="form-label">No. WhatsApp (Opsional):</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fab fa-whatsapp"></i>
                                        </span>
                                        <input type="text" class="form-control" id="no_whatsapp"
                                            name="no_whatsapp" placeholder="08123456789">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-warning btn-kasir w-100 btn-lg">
                                    <i class="fas fa-cash-register"></i> PROSES PEMBAYARAN
                                </button>
                            </form>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-credit-card fa-4x mb-3" style="opacity: 0.3;"></i>
                                <h6 style="opacity: 0.7;">Belum Ada Transaksi</h6>
                                <p style="opacity: 0.7;">Tambahkan barang untuk memulai pembayaran</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Logout -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="logoutModalLabel">
                        <i class="fas fa-exclamation-triangle"></i> Konfirmasi Logout
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="fas fa-user-times fa-4x text-danger mb-3"></i>
                        <h5>Yakin ingin keluar dari sistem kasir?</h5>
                        <p class="text-muted">Semua data keranjang akan hilang jika Anda logout</p>
                    </div>

                    <!-- Info Session -->
                    @if (session('cart') && count(session('cart')) > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-shopping-cart"></i>
                            Anda memiliki <strong>{{ count(session('cart')) }} item</strong> di keranjang
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-6">
                            <p class="small text-muted">
                                <strong>Kasir:</strong><br>
                                {{ Auth::user()->name }}
                            </p>
                        </div>
                        <div class="col-6">
                            <p class="small text-muted">
                                <strong>Waktu Login:</strong><br>
                                {{ date('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary btn-kasir" data-bs-dismiss="modal">
                        <i class="fas fa-arrow-left"></i> Batal
                    </button>
                    <a href="{{ route('kasir.logout') }}" class="btn btn-danger btn-kasir">
                        <i class="fas fa-power-off"></i> Ya, Keluar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Kosongkan Keranjang -->
    <div class="modal fade" id="clearCartModal" tabindex="-1" aria-labelledby="clearCartModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="clearCartModalLabel">
                        <i class="fas fa-trash"></i> Kosongkan Keranjang
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-shopping-cart fa-4x text-warning mb-3"></i>
                    <h5>Hapus semua barang dari keranjang?</h5>
                    <p class="text-muted">Tindakan ini tidak dapat dibatalkan</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary btn-kasir" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <form action="{{ route('kasir.clear-cart') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-kasir">
                            <i class="fas fa-trash"></i> Ya, Kosongkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto hide alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Hitung kembalian
            const hitungKembalianBtn = document.getElementById('hitungKembalianBtn');
            if (hitungKembalianBtn) {
                hitungKembalianBtn.addEventListener('click', function() {
                    const total = parseFloat(document.getElementById('total_checkout').value);
                    const tunai = parseFloat(document.getElementById('tunai').value);

                    if (!isNaN(tunai) && !isNaN(total)) {
                        const kembalian = tunai - total;
                        document.getElementById('kembalian').value = formatRupiah(kembalian);
                    }
                });
            }

            // Auto calculate kembalian saat tunai berubah
            const tunaiInput = document.getElementById('tunai');
            if (tunaiInput) {
                tunaiInput.addEventListener('input', function() {
                    const total = parseFloat(document.getElementById('total_checkout').value);
                    const tunai = parseFloat(this.value);

                    if (!isNaN(tunai) && !isNaN(total)) {
                        const kembalian = tunai - total;
                        document.getElementById('kembalian').value = formatRupiah(kembalian);
                    }
                });
            }

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }

            // Update tombol kosongkan keranjang untuk menggunakan modal
            document.addEventListener('click', function(e) {
                if (e.target.closest('.clear-cart-btn')) {
                    e.preventDefault();
                    var clearCartModal = new bootstrap.Modal(document.getElementById('clearCartModal'));
                    clearCartModal.show();
                }
            });
        });
    </script>
</body>

</html>
