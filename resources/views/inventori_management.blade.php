<!-- resources/views/inventori_management.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="inventoryTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="safety-stock-tab" data-bs-toggle="tab"
                                    data-bs-target="#safety-stock" type="button" role="tab"
                                    aria-controls="safety-stock" aria-selected="true">Safety Stock</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reorder-point-tab" data-bs-toggle="tab"
                                    data-bs-target="#reorder-point" type="button" role="tab"
                                    aria-controls="reorder-point" aria-selected="false">Reorder Point</button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="tab-content" id="inventoryTabsContent">
                            <!-- Safety Stock Tab -->
                            <div class="tab-pane fade show active" id="safety-stock" role="tabpanel"
                                aria-labelledby="safety-stock-tab">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Data Safety Stock</h5>
                                    <a href="{{ route('safety-stock.create') }}" class="btn btn-primary">Tambah Safety
                                        Stock</a>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Barang</th>
                                                <th>Pemakaian Maksimum</th>
                                                <th>Pemakaian rata-rata perhari</th>
                                                <th>Lead Time</th>
                                                <th>Hasil</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($safetyStocks as $index => $ss)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $ss->barang->nama_barang }} - {{ $ss->barang->merek }}</td>
                                                    <td>{{ number_format($ss->pemakaian_maksimum, 0) }}
                                                        {{ $ss->barang->satuan }}</td>
                                                    <td>{{ number_format($ss->pemakaian_rata_rata, 0) }}
                                                        {{ $ss->barang->satuan }}</td>
                                                    <td>{{ $ss->lead_time }} hari</td>
                                                    <td>{{ number_format($ss->hasil, 0) }} {{ $ss->barang->satuan }}</td>
                                                    <td>
                                                        <form action="{{ route('safety-stock.calculate', $ss->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-info">Hitung
                                                                Ulang</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">Tidak ada data</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Reorder Point Tab -->
                            <div class="tab-pane fade" id="reorder-point" role="tabpanel"
                                aria-labelledby="reorder-point-tab">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Data Reorder Point</h5>
                                    <a href="{{ route('reorder-point.create') }}" class="btn btn-primary">Tambah Reorder
                                        Point</a>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Barang</th>
                                                <th>Safety Stock</th>
                                                <th>Lead Time</th>
                                                <th>Hasil</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($reorderPoints as $index => $rop)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $rop->barang->nama_barang }} - {{ $rop->barang->merek }}</td>
                                                    <td>{{ number_format($rop->safety_stock, 0) }}
                                                        {{ $rop->barang->satuan }}</td>
                                                    <td>{{ $rop->lead_time }} hari</td>
                                                    <td>{{ number_format($rop->hasil, 0) }} {{ $rop->barang->satuan }}
                                                    </td>
                                                    <td>
                                                        <form action="{{ route('reorder-point.calculate', $rop->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-info">Hitung
                                                                Ulang</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Tidak ada data</td>
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
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Persist active tab in URL or session storage
        $(document).ready(function() {
            // Check for tab parameter in URL
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');

            if (tabParam === 'reorder-point') {
                // Activate reorder point tab
                $('#reorder-point-tab').tab('show');
            }

            // Store active tab when changed
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const activeTab = $(e.target).attr('id');
                const tabName = activeTab.replace('-tab', '');

                // Update URL without reloading page
                const url = new URL(window.location.href);
                url.searchParams.set('tab', tabName);
                window.history.pushState({}, '', url);
            });
        });
    </script>
@endpush
