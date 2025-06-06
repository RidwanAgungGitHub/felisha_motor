<!-- resources/views/reports/inventory_status.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Status Inventori</h5>
                        <div>
                            <a href="{{ route('reports.inventory-status.print') }}" class="btn btn-sm btn-secondary"
                                target="_blank">
                                <i class="fa fa-print"></i> Cetak
                            </a>
                            <a href="{{ route('reports.inventory-status.export') }}" class="btn btn-sm btn-success">
                                <i class="fa fa-file-excel"></i> Export Excel
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="inventory-status-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Merek</th>
                                        <th>Stok Saat Ini</th>
                                        <th>Safety Stock</th>
                                        <th>Reorder Point</th>
                                        <th>Period</th>
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
                                            <td>{{ $item['period'] ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $item['status_class'] }}">
                                                    {{ $item['status'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#inventory-status-table').DataTable({
                responsive: true,
                ordering: true,
                searching: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                }
            });
        });
    </script>
@endpush
