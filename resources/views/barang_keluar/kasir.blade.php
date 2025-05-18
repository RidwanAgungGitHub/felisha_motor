@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-cash-register"></i> Kasir - Transaksi Barang Keluar</h5>
                </div>
                <div class="card-body">
                    <!-- Form untuk memilih dan menambahkan barang ke keranjang -->
                    <form action="{{ route('kasir.add-to-cart') }}" method="POST">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="barang_id">Pilih Barang:</label>
                                    <select id="barang_id" name="barang_id" class="form-control" required>
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach ($data as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->nama_barang }} - {{ $item->merek }} - Rp {{ number_format($item->harga, 0, ',', '.') }} (Stok: {{ $item->stok }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-success btn-block">
                                        <i class="fas fa-plus"></i> Tambah ke Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Daftar Barang dalam Keranjang -->
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Daftar Barang dalam Keranjang</h6>
                                @if(session('cart') && count(session('cart')) > 0)
                                <form action="{{ route('kasir.clear-cart') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Kosongkan Keranjang
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="cart-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="10%">Kode</th>
                                            <th width="25%">Nama Barang</th>
                                            <th width="15%">Harga</th>
                                            <th width="15%">Jumlah</th>
                                            <th width="15%">Subtotal</th>
                                            <th width="15%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(session('cart') && count(session('cart')) > 0)
                                            @php $no = 1; $total = 0; @endphp
                                            @foreach(session('cart') as $id => $item)
                                                @php
                                                    $subtotal = $item['harga'] * $item['jumlah'];
                                                    $total += $subtotal;
                                                @endphp
                                                <tr>
                                                    <td>{{ $no++ }}</td>
                                                    <td>{{ $item['kode'] }}</td>
                                                    <td>{{ $item['nama_barang'] }}</td>
                                                    <td>Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                                                    <td>
                                                        <form action="{{ route('kasir.update-cart-qty') }}" method="POST" class="d-flex">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $id }}">
                                                            <input type="number" name="jumlah" value="{{ $item['jumlah'] }}"
                                                                min="1" max="{{ $item['stok_tersedia'] }}"
                                                                class="form-control form-control-sm" style="width: 70px;">
                                                            <button type="submit" class="btn btn-sm btn-info ml-1">
                                                                <i class="fas fa-sync"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                    <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                                    <td>
                                                        <form action="{{ route('kasir.remove-from-cart') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $id }}">
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-trash"></i> Hapus
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="bg-light font-weight-bold">
                                                <td colspan="5" class="text-right">Total</td>
                                                <td colspan="2">Rp {{ number_format($total, 0, ',', '.') }}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    <div class="py-3">
                                                        <i class="fas fa-shopping-cart fa-3x text-muted"></i>
                                                        <p class="mt-2 text-muted">Keranjang belanja masih kosong</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
        <div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-credit-card"></i> Pembayaran</h5>
    </div>
    <div class="card-body">
        @if(session('cart') && count(session('cart')) > 0)
            @php $total = 0; @endphp
            @foreach(session('cart') as $item)
                @php $total += $item['harga'] * $item['jumlah']; @endphp
            @endforeach

            <!-- Form checkout untuk proses pembayaran -->
            <form action="{{ route('kasir.checkout') }}" method="POST" id="checkoutForm">
                @csrf
                <div class="form-group">
                    <label>Total Belanja:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="text" class="form-control" value="{{ number_format($total, 0, ',', '.') }}" readonly>
                        <input type="hidden" name="total" id="total_checkout" value="{{ $total }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="tunai_checkout">Jumlah Tunai:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="number" class="form-control" id="tunai_checkout" name="tunai" value="{{ $total }}" min="{{ $total }}" required>
                    </div>
                    <small class="text-muted">Masukkan jumlah tunai yang dibayarkan</small>
                </div>

                <div class="form-group">
                    <label for="kembalian">Kembalian:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="text" class="form-control" id="kembalian" value="{{ session('kembalian') ? number_format(session('kembalian'), 0, ',', '.') : '0' }}" readonly>
                    </div>
                </div>

                <div class="form-group">
    <label for="nama_pelanggan">Nama Pelanggan (Opsional):</label>
    <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" placeholder="Masukkan nama pelanggan">
</div>

<div class="form-group">
    <label for="no_whatsapp">Nomor WhatsApp (Opsional):</label>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
        </div>
        <input type="text" class="form-control" id="no_whatsapp" name="no_whatsapp" placeholder="Contoh: 08123456789">
    </div>
    <small class="text-muted">Format: 08xx atau +62xxx</small>
</div>

                <!-- Tombol hitung kembalian (bukan form terpisah) -->
                <button type="button" class="btn btn-info btn-block mb-3" id="hitungKembalianBtn">
                    <i class="fas fa-calculator"></i> Hitung Kembalian
                </button>

                <button type="submit" class="btn btn-success btn-block mt-4">
                    <i class="fas fa-cash-register"></i> Proses Pembayaran
                </button>
            </form>
        @else
            <div class="text-center py-4">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <p>Belum ada barang di keranjang</p>
                <p class="text-muted">Tambahkan barang terlebih dahulu untuk melakukan pembayaran</p>
            </div>
        @endif
    </div>
</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- JavaScript untuk menghitung kembalian tanpa submit form -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hitungKembalianBtn = document.getElementById('hitungKembalianBtn');

    if (hitungKembalianBtn) {
        hitungKembalianBtn.addEventListener('click', function() {
            const total = parseFloat(document.getElementById('total_checkout').value);
            const tunai = parseFloat(document.getElementById('tunai_checkout').value);

            if (!isNaN(tunai) && !isNaN(total)) {
                const kembalian = tunai - total;
                document.getElementById('kembalian').value = formatRupiah(kembalian);
            }
        });
    }

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }
});
</script>
@endsection