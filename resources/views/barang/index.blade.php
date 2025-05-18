<!-- index.blade.php -->
@extends('layouts.app')

@section('title', 'Daftar Barang')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Barang</h6>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                    data-bs-target="#createBarangModal">
                    <i class="fas fa-plus"></i> Tambah Barang
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
                                <th>Merek</th>
                                <th>Satuan/Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($barang as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->nama_barang }}</td>
                                    <td>{{ $item->merek }}</td>
                                    <td>{{ $item->satuan }} / Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($item->stok < 10)
                                            <span class="badge bg-danger">{{ $item->stok }}</span>
                                        @else
                                            {{ $item->stok }}
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#showBarangModal{{ $item->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editBarangModal{{ $item->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a class="btn btn-success btn-sm"
                                                href="{{ route('barang_masuk.create.from.barang', $item->id) }}">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                            <form action="{{ route('barang.destroy', $item->id) }}" method="POST"
                                                class="d-inline delete-form" data-name="{{ $item->nama_barang }}">
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
                                    <td colspan="6" class="text-center">Tidak ada data barang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Create Modal -->
    @include('barang.create')

    <!-- Include Modals for each item -->
    @if ($barang->count() > 0)
        @foreach ($barang as $modalItem)
            @include('barang.show', ['barang' => $modalItem])
            @include('barang.edit', ['barang' => $modalItem])
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
        });
    </script>
@endsection
