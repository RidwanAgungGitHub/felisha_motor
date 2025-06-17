@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </div>

        <!-- Dashboard Cards -->
        <div class="row">
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card card-dashboard border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Barang</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBarang }} Jenis</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-boxes fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card card-dashboard border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Stok</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalStok) }} Unit
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-warehouse fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card card-dashboard border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Reorder Point</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($notifications) }} Barang</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-bell mr-2"></i>Notifikasi Reorder Point
                        </h6>
                        <a href="{{ route('reports.inventory-status') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-chart-bar mr-1"></i>Lihat Laporan Lengkap
                        </a>
                    </div>
                    <div class="card-body">
                        @if (count($notifications) > 0)
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Perhatian!</strong> Terdapat {{ count($notifications) }} barang yang telah mencapai
                                reorder point dan perlu segera dipesan ulang.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="35%">Nama Barang</th>
                                        <th width="25%">Merek</th>
                                        <th width="15%">Stok Saat Ini</th>
                                        <th width="15%">Reorder Point</th>
                                        <th width="5%" colspan="2">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($notifications as $index => $notification)
                                        <tr class="notification-row" data-priority="{{ $notification['priority'] }}">
                                            <td class="text-center text-dark">{{ $index + 1 }}</td>
                                            <td class="text-dark">
                                                <strong>{{ $notification['nama_barang'] }}</strong>
                                                <i class="fas fa-exclamation-circle text-warning ml-1"
                                                    title="Reorder Point Reached!"></i>
                                            </td>
                                            <td class="text-dark">{{ $notification['merek'] }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-warning badge-pill text-dark">
                                                    {{ $notification['stok_saat_ini'] }} {{ $notification['satuan'] }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <strong class="text-warning">
                                                    {{ $notification['reorder_point'] ? number_format($notification['reorder_point']) . ' ' . $notification['satuan'] : '-' }}
                                                </strong>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('dashboard') }}?barang_id={{ $notification['id'] }}#createBarangMasukModal"
                                                    class="btn btn-sm btn-success" title="Tambah Barang Masuk"
                                                    onclick="setTimeout(function(){ $('#createBarangMasukModal').modal('show'); }, 100);">
                                                    <i class="fas fa-plus"></i>
                                                </a>

                                            </td>
                                            <td class="text-center">
                                                <i class="bi bi-whatsapp text-success" style="cursor: pointer;"
                                                    data-barang-id="{{ $notification['id'] }}"
                                                    data-barang-nama="{{ $notification['nama_barang'] }}"
                                                    onclick="openWhatsappModal(this)">
                                                </i>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                                    <h5 class="text-dark">Semua Barang Masih Aman</h5>
                                                    <p class="text-dark">Tidak ada barang yang mencapai reorder point saat
                                                        ini.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if (count($notifications) > 0)
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <strong>Keterangan:</strong>
                                    <span class="badge badge-warning ml-2 text-dark">Reorder Point</span> = Titik pemesanan
                                    ulang
                                    barang (Segera pesan ulang!)
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Barang Masuk Modal -->
    <div class="modal fade" id="createBarangMasukModal" tabindex="-1" aria-labelledby="createBarangMasukModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createBarangMasukModalLabel">
                        @if ($selectedBarang)
                            Tambah Barang Masuk - <span class="text-primary">{{ $selectedBarang->nama_barang }}
                                ({{ $selectedBarang->merek }})</span>
                        @else
                            Tambah Barang Masuk
                        @endif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($showReorderAlert)
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Perhatian!</strong> Barang ini telah mencapai reorder point.
                            <br><small>Stok saat ini: <strong>{{ $selectedBarang->stok }}
                                    {{ $selectedBarang->satuan }}</strong>. Disarankan untuk memesan segera.</small>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('barang_masuk.store') }}" method="POST" id="createBarangMasukForm">
                        @csrf
                        <div class="mb-3">
                            <label for="barang_id" class="form-label">Pilih Barang</label>
                            <select class="form-select" id="barang_id" name="barang_id" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($allBarangs as $barang)
                                    <option value="{{ $barang->id }}"
                                        {{ $selectedBarang && $selectedBarang->id == $barang->id ? 'selected' : '' }}>
                                        {{ $barang->nama_barang }} - {{ $barang->merek }} (Stok: {{ $barang->stok }}
                                        {{ $barang->satuan }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pilih barang yang akan ditambah stoknya
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Pilih Supplier</label>
                            <div class="input-group">
                                <select class="form-select" id="supplier_id" name="supplier_id" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->nama }} - {{ $supplier->kontak }}
                                        </option>
                                    @endforeach
                                </select>
                                <a href="{{ route('supplier.create') }}" target="_blank"
                                    class="btn btn-outline-secondary">
                                    <i class="fas fa-plus"></i> Tambah Supplier
                                </a>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="harga" class="form-label">Harga Per Unit</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" step="0.01" class="form-control" id="harga"
                                        name="harga" value="{{ old('harga', $defaultValues['harga']) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah"
                                    value="{{ old('jumlah', $defaultValues['jumlah']) }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tanggal_beli" class="form-label">Tanggal Pembelian</label>
                                <input type="date" class="form-control" id="tanggal_beli" name="tanggal_beli"
                                    value="{{ old('tanggal_beli', $defaultValues['tanggal_beli']) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="estimasi_datang" class="form-label">Estimasi Kedatangan</label>
                                <input type="date" class="form-control" id="estimasi_datang" name="estimasi_datang"
                                    value="{{ old('estimasi_datang', $defaultValues['estimasi_datang']) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending"
                                    {{ old('status', $defaultValues['status']) == 'pending' ? 'selected' : '' }}>
                                    Pending (Belum Diterima)
                                </option>
                                <option value="diterima"
                                    {{ old('status', $defaultValues['status']) == 'diterima' ? 'selected' : '' }}>
                                    Diterima (Langsung Update Stok)
                                </option>
                            </select>
                            <small class="text-muted">
                                Pilih "Diterima" jika barang sudah datang dan siap untuk menambah stok.
                                @if ($selectedBarang)
                                    <br><strong>Catatan:</strong> Untuk barang reorder point, disarankan pilih "Pending"
                                    terlebih dulu.
                                @endif
                            </small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" form="createBarangMasukForm" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Ganti modal WhatsApp yang sudah ada dengan yang ini -->
    <div class="modal fade" id="createWhatsappModal" tabindex="-1" aria-labelledby="createWhatsappModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createWhatsappModalLabel">
                        Pilih Supplier
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informasi:</strong> Pilih supplier untuk mengirim permintaan penawaran barang via WhatsApp.
                    </div>

                    <table class="table table-bordered table-hover" id="supplierDataTable" width="100%"
                        cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="10%">No</th>
                                <th width="35%">Nama</th>
                                <th width="35%">Kontak</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($suppliers as $supplier)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $supplier->nama }}</strong>
                                        @if ($supplier->alamat)
                                            <br><small class="text-muted">{{ $supplier->alamat }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($supplier->telp_1)
                                            <div class="mb-1">
                                                <i class="fas fa-phone text-primary"></i>
                                                <span class="text-dark">{{ $supplier->telp_1 }}</span>
                                            </div>
                                        @endif

                                        @if ($supplier->kontak)
                                            <div>
                                                <i class="fas fa-address-book text-success"></i>
                                                <span class="text-dark">{{ $supplier->kontak }}</span>
                                            </div>
                                        @endif

                                        @if (!$supplier->telp_1 && !$supplier->kontak)
                                            <span class="badge badge-secondary">Tidak ada kontak</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $phoneNumber = $supplier->telp_1 ?: $supplier->kontak;
                                        @endphp

                                        @if ($phoneNumber)
                                            <button class="btn btn-success btn-sm"
                                                onclick="sendWhatsappToSupplier('{{ $phoneNumber }}', '{{ $supplier->nama }}')"
                                                title="Kirim WhatsApp ke {{ $supplier->nama }}">
                                                <i class="fab fa-whatsapp"></i> Kirim
                                            </button>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled
                                                title="Tidak ada nomor telepon">
                                                <i class="fas fa-ban"></i> N/A
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-2x mb-3"></i>
                                            <h6>Belum Ada Supplier</h6>
                                            <p>Silakan tambahkan supplier terlebih dahulu.</p>
                                            <a href="{{ route('supplier.create') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus"></i> Tambah Supplier
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if (request()->has('barang_id'))
        <script>
            // Auto open modal if barang_id parameter exists
            document.addEventListener('DOMContentLoaded', function() {
                var modal = new bootstrap.Modal(document.getElementById('createBarangMasukModal'));
                modal.show();
            });
        </script>
    @endif
@endsection

@push('styles')
    <style>
        .notification-row[data-priority="1"] {
            background-color: #fff3cd !important;
            border-left: 4px solid #ffc107;
        }

        .card-dashboard {
            transition: transform 0.2s;
        }

        .card-dashboard:hover {
            transform: translateY(-2px);
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge-pill {
            font-size: 0.85em;
            font-weight: 600;
        }

        /* Override white text colors */
        .text-white {
            color: #495057 !important;
        }

        .badge-warning.text-dark {
            color: #212529 !important;
        }
    </style>
@endpush
<script>
    let selectedBarangId = null;
    let selectedBarangNama = null;
    let originalModalContent = null; // Simpan konten asli modal

    // Simpan konten asli modal saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        const modalBody = document.querySelector('#createWhatsappModal .modal-body');
        if (modalBody) {
            originalModalContent = modalBody.innerHTML;
        }
    });

    function openWhatsappModal(element) {
        selectedBarangId = element.getAttribute('data-barang-id');
        selectedBarangNama = element.getAttribute('data-barang-nama');

        // Update modal title
        document.getElementById('createWhatsappModalLabel').textContent =
            'Pilih Supplier untuk: ' + selectedBarangNama;

        // Show modal
        var modal = new bootstrap.Modal(document.getElementById('createWhatsappModal'));
        modal.show();
    }

    function sendWhatsappToSupplier(supplierPhone, supplierName) {
        if (!selectedBarangId) {
            alert('Barang belum dipilih!');
            return;
        }

        // Show loading
        const loadingHtml = `
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Mengirim pesan WhatsApp...</p>
        </div>
    `;

        document.querySelector('#createWhatsappModal .modal-body').innerHTML = loadingHtml;

        // Send AJAX request
        fetch('/dashboard/send-whatsapp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    supplier_phone: supplierPhone,
                    supplier_name: supplierName,
                    barang_id: selectedBarangId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const successHtml = `
                    <div class="text-center py-4">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                            <h5>Berhasil Dikirim!</h5>
                            <p>${data.message}</p>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                `;
                    document.querySelector('#createWhatsappModal .modal-body').innerHTML = successHtml;

                    // Auto close after 3 seconds
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('createWhatsappModal')).hide();
                        location.reload(); // Refresh halaman
                    }, 3000);

                } else {
                    // Show error message
                    const errorHtml = `
                    <div class="text-center py-4">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                            <h5>Gagal Mengirim</h5>
                            <p>${data.message}</p>
                        </div>
                        <button type="button" class="btn btn-secondary" onclick="location.reload()">Coba Lagi</button>
                    </div>
                `;
                    document.querySelector('#createWhatsappModal .modal-body').innerHTML = errorHtml;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errorHtml = `
                <div class="text-center py-4">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                        <h5>Terjadi Kesalahan</h5>
                        <p>Tidak dapat mengirim pesan WhatsApp. Silakan coba lagi.</p>
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="location.reload()">Coba Lagi</button>
                </div>
            `;
                document.querySelector('#createWhatsappModal .modal-body').innerHTML = errorHtml;
            });
    }

    // Reset modal when closed
    document.getElementById('createWhatsappModal').addEventListener('hidden.bs.modal', function() {
        selectedBarangId = null;
        selectedBarangNama = null;

        // Reset modal title
        document.getElementById('createWhatsappModalLabel').textContent = 'Pilih Supplier';

        // Reset modal content dengan konten asli
        if (originalModalContent) {
            document.querySelector('#createWhatsappModal .modal-body').innerHTML = originalModalContent;
        }
    });
</script>
