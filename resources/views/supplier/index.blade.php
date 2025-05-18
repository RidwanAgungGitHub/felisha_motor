@extends('layouts.app')

@section('title', 'Data Supplier')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Data Supplier</h1>
            <a href="{{ route('supplier.create') }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Supplier
            </a>
        </div>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Supplier</h6>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered" id="supplierDataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Kontak</th>
                                <th>Alamat</th>
                                <th>Kota</th>
                                <th>Telp</th>
                                <th>Email</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $supplier->nama }}</td>
                                    <td>{{ $supplier->kontak }}</td>
                                    <td>{{ $supplier->alamat }}</td>
                                    <td>{{ $supplier->kota }}, {{ $supplier->provinsi }}</td>
                                    <td>{{ $supplier->telp_1 }}</td>
                                    <td>{{ $supplier->email ?? '-' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('supplier.show', $supplier->id) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('supplier.edit', $supplier->id) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('supplier.destroy', $supplier->id) }}" method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada data supplier</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $suppliers->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#supplierDataTable').DataTable({
                "paging": false,
                "info": false
            });
        });
    </script>
@endpush
