@extends('layouts.app')

@section('title', 'Data Barang Masuk')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Barang Masuk</h6>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                    data-bs-target="#createBarangMasukModal">
                    <i class="fas fa-plus"></i> Tambah Barang Masuk
                </button>
            </div>
            <div class="card-body">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Supplier</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Total</th>
                                <th>Tanggal Beli</th>
                                <th>Estimasi Datang</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barangMasuk as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->barang->nama_barang }}</td>
                                    <td>
                                        @if ($item->supplier)
                                            {{ $item->supplier->nama }} -
                                            {{ $item->supplier->kontak }}
                                        @else
                                            <span class="text-danger">Supplier tidak tersedia</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->jumlah }}</td>
                                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->jumlah * $item->harga, 0, ',', '.') }}</td>
                                    <td>{{ date('d/m/Y', strtotime($item->tanggal_beli)) }}</td>
                                    <td>{{ date('d/m/Y', strtotime($item->estimasi_datang)) }}</td>
                                    <td>
                                        @if ($item->status == 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($item->status == 'diterima')
                                            <span class="badge bg-success">Diterima</span>
                                        @else
                                            <span
                                                class="badge bg-secondary">{{ $item->status ?? 'Tidak Ada Status' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#showBarangMasukModal{{ $item->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editBarangMasukModal{{ $item->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('barang_masuk.destroy', $item->id) }}" method="POST"
                                                class="d-inline delete-form" data-name="{{ $item->barang->nama_barang }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm delete-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data barang masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $barangMasuk->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Include Create Modal -->
    @include('barang_masuk.create')

    <!-- Include Modals for each item -->
    @if ($barangMasuk->count() > 0)
        @foreach ($barangMasuk as $modalItem)
            @include('barang_masuk.show', ['barangMasuk' => $modalItem])
            @include('barang_masuk.edit', ['barangMasuk' => $modalItem])
        @endforeach
    @endif
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle delete confirmations
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('form');
                    const itemName = form.getAttribute('data-name');

                    if (confirm(`Apakah Anda yakin ingin menghapus data "${itemName}"?`)) {
                        form.submit();
                    }
                });
            });

            // Setup total price calculations for modals
            document.querySelectorAll('.modal').forEach(modal => {
                const modalId = modal.id;
                if (!modalId || !modalId.startsWith('editBarangMasukModal')) return;

                // Extract the item ID from the modal ID
                const itemId = modalId.replace('editBarangMasukModal', '');

                // Calculate total when inputs change
                const jumlahInput = document.getElementById(`jumlah${itemId}`);
                const hargaInput = document.getElementById(`harga${itemId}`);

                if (!jumlahInput || !hargaInput) return;

                const calculateTotal = function() {
                    const jumlah = parseFloat(jumlahInput.value) || 0;
                    const harga = parseFloat(hargaInput.value) || 0;
                    const total = jumlah * harga;

                    let totalPreview = document.getElementById(`total_preview${itemId}`);
                    if (totalPreview) {
                        totalPreview.textContent = total.toLocaleString('id-ID');
                    }
                };

                // Add event listeners
                jumlahInput.addEventListener('input', calculateTotal);
                hargaInput.addEventListener('input', calculateTotal);

                // Initialize on modal show
                modal.addEventListener('shown.bs.modal', calculateTotal);
            });
        });
    </script>
@endsection
