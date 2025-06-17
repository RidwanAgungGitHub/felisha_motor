@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <!-- Card untuk filter -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Filter Data Barang Keluar</h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('barang-keluar.index') }}" method="GET" class="row align-items-end">
                            <!-- Filter Bulan -->
                            <div class="col-md-3 mb-3">
                                <label for="bulan">Pilih Bulan</label>
                                <select name="bulan" id="bulan" class="form-control">
                                    @php
                                        $namaBulan = [
                                            '01' => 'Januari',
                                            '02' => 'Februari',
                                            '03' => 'Maret',
                                            '04' => 'April',
                                            '05' => 'Mei',
                                            '06' => 'Juni',
                                            '07' => 'Juli',
                                            '08' => 'Agustus',
                                            '09' => 'September',
                                            '10' => 'Oktober',
                                            '11' => 'November',
                                            '12' => 'Desember',
                                        ];
                                    @endphp
                                    @foreach ($namaBulan as $kode => $nama)
                                        <option value="{{ $kode }}" {{ $bulan == $kode ? 'selected' : '' }}>
                                            {{ $nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Tahun -->
                            <div class="col-md-3 mb-3">
                                <label for="tahun">Pilih Tahun</label>
                                <select name="tahun" id="tahun" class="form-control">
                                    @foreach ($availableYears as $year)
                                        <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tombol Filter -->
                            <div class="col-md-6 mb-3">
                                <button type="submit" class="btn btn-primary" name="tab" value="{{ $activeTab ?? 'histori' }}">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('barang-keluar.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-sync"></i> Reset
                                </a>
                                <!-- TAMBAHKAN TOMBOL EXPORT INI -->
                                <a href="{{ route('barang-keluar.export', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                    <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Import Data Barang Keluar</h5>
                </div>
                <div class="card-body">
                    <!-- Alert untuk success -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Alert untuk error -->
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Form Import -->
                    <form action="{{ route('barang-keluar.import') }}" method="POST" enctype="multipart/form-data" class="row align-items-end">
                        @csrf
                        <div class="col-md-6 mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror"
                                accept=".xlsx,.xls,.csv" required>
                            <small class="form-text text-muted">
                                Format yang didukung: .xlsx, .xls, .csv (Max: 2MB)
                            </small>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-upload"></i> Import Excel
                            </button>
                            <a href="{{ route('download.template') }}" class="btn btn-info">
                                <i class="fas fa-download"></i> Download Template
                            </a>
                        </div>
                    </form>

                    <!-- Panduan Import -->
                    <div class="mt-3">
                        <h6><i class="fas fa-info-circle"></i> Panduan Import:</h6>
                        <ul class="text-muted small">
                            <li>Download template terlebih dahulu untuk format yang benar</li>
                            <li>Pastikan nama barang dan merek sesuai dengan data di sistem</li>
                            <li>Format tanggal: DD/MM/YYYY (contoh: 17/06/2025)</li>
                            <li>Jumlah harus berupa angka dan tidak melebihi stok tersedia</li>
                            <li>Kolom yang wajib diisi: nama_barang, merek, jumlah, tanggal</li>
                        </ul>
                    </div>

                    <!-- Error Import -->
                    @if(session('import_errors'))
                        <div class="alert alert-warning mt-3">
                            <h6><i class="fas fa-exclamation-triangle"></i> Detail Error Import:</h6>
                            <div class="max-height-200 overflow-auto">
                                <ul class="mb-0 small">
                                    @foreach(session('import_errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <style>
            .max-height-200 {
                max-height: 200px;
            }
            </style>

                <!-- Card dengan tab untuk histori dan ringkasan barang keluar -->
                <div class="card">
                    <div class="card-header p-0">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab == 'histori' ? 'active' : '' }}"
                                    href="{{ route('barang-keluar.index', ['bulan' => $bulan, 'tahun' => $tahun, 'tab' => 'histori']) }}">
                                    Data Histori Barang Keluar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab == 'ringkasan' ? 'active' : '' }}"
                                    href="{{ route('barang-keluar.index', ['bulan' => $bulan, 'tahun' => $tahun, 'tab' => 'ringkasan']) }}">
                                    Ringkasan Barang Keluar
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="tab-content">
                            <!-- Tab untuk histori barang keluar -->
                            <div class="tab-pane fade {{ $activeTab == 'histori' ? 'show active' : '' }}" id="histori"
                                role="tabpanel" aria-labelledby="histori-tab">
                                <h5 class="mb-3">
                                    Data Histori Bulan {{ $namaBulan[$bulan] ?? '' }} {{ $tahun }}
                                    <small class="text-muted">({{ $barangKeluar->total() }} total data)</small>
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Barang</th>
                                                <th>Merek</th>
                                                <th>Jumlah</th>
                                                <th>Harga Satuan</th>
                                                <th>Total Harga</th>
                                                <th>Tanggal</th>
                                                <th>Keterangan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($barangKeluar as $index => $item)
                                                <tr>
                                                    <td>{{ ($barangKeluar->currentPage() - 1) * $barangKeluar->perPage() + $index + 1 }}
                                                    </td>
                                                    <td>{{ $item->barang->nama_barang }}</td>
                                                    <td>{{ $item->barang->merek }}</td>
                                                    <td>{{ $item->jumlah }} {{ $item->barang->satuan }}</td>
                                                    <td>Rp {{ number_format($item->barang->harga, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                                    <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                                    <td>
                                                        <a href="{{ route('barang-keluar.show', $item->id) }}"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">Tidak ada data barang keluar
                                                        untuk bulan {{ $namaBulan[$bulan] }} {{ $tahun }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination untuk histori -->
                                @if ($barangKeluar->hasPages())
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <div class="dataTables_info">
                                                <small class="text-muted">
                                                    Menampilkan {{ $barangKeluar->firstItem() }} sampai
                                                    {{ $barangKeluar->lastItem() }}
                                                    dari {{ $barangKeluar->total() }} data histori
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="dataTables_paginate paging_simple_numbers float-right">
                                                <nav aria-label="Pagination Navigation">
                                                    <ul class="pagination pagination-sm justify-content-end mb-0">
                                                        {{-- Previous Page Link --}}
                                                        @if ($barangKeluar->onFirstPage())
                                                            <li class="page-item disabled">
                                                                <span class="page-link">
                                                                    <i class="fas fa-angle-left"></i>
                                                                </span>
                                                            </li>
                                                        @else
                                                            <li class="page-item">
                                                                <a class="page-link"
                                                                    href="{{ $barangKeluar->previousPageUrl() }}">
                                                                    <i class="fas fa-angle-left"></i>
                                                                </a>
                                                            </li>
                                                        @endif

                                                        {{-- Pagination Elements --}}
                                                        @foreach ($barangKeluar->getUrlRange(1, $barangKeluar->lastPage()) as $page => $url)
                                                            @if ($page == $barangKeluar->currentPage())
                                                                <li class="page-item active">
                                                                    <span class="page-link">{{ $page }}</span>
                                                                </li>
                                                            @else
                                                                <li class="page-item">
                                                                    <a class="page-link"
                                                                        href="{{ $url }}">{{ $page }}</a>
                                                                </li>
                                                            @endif
                                                        @endforeach

                                                        {{-- Next Page Link --}}
                                                        @if ($barangKeluar->hasMorePages())
                                                            <li class="page-item">
                                                                <a class="page-link"
                                                                    href="{{ $barangKeluar->nextPageUrl() }}">
                                                                    <i class="fas fa-angle-right"></i>
                                                                </a>
                                                            </li>
                                                        @else
                                                            <li class="page-item disabled">
                                                                <span class="page-link">
                                                                    <i class="fas fa-angle-right"></i>
                                                                </span>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Info pagination histori tanpa pagination -->
                                    @if ($barangKeluar->total() > 0)
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                Menampilkan {{ $barangKeluar->total() }} data histori
                                            </small>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <!-- Tab untuk ringkasan bulanan barang keluar -->
                            <div class="tab-pane fade {{ $activeTab == 'ringkasan' ? 'show active' : '' }}" id="ringkasan"
                                role="tabpanel" aria-labelledby="ringkasan-tab">
                                <h5 class="mb-3">
                                    Ringkasan Barang Keluar Bulan {{ $namaBulan[$bulan] ?? '' }} {{ $tahun }}
                                    <small class="text-muted">(Total {{ $totalHariAktif }} hari aktif -
                                        {{ $barangKeluarBulanan->total() }} jenis barang)</small>
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Barang</th>
                                                <th>Merek</th>
                                                <th>Jumlah</th>
                                                <th>Rata-Rata/Hari Aktif</th>
                                                <th>Total Harga</th>
                                                <th>Periode</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($barangKeluarBulanan as $index => $item)
                                                <tr>
                                                    <td>{{ ($barangKeluarBulanan->currentPage() - 1) * $barangKeluarBulanan->perPage() + $index + 1 }}
                                                    </td>
                                                    <td>{{ $item->nama_barang }}</td>
                                                    <td>{{ $item->merek }}</td>
                                                    <td>{{ $item->total_jumlah }} {{ $item->satuan }}</td>
                                                    <td>{{ $item->rata_per_hari }} {{ $item->satuan }}/hari
                                                        ({{ $item->jumlah_hari_aktif }} hari)
                                                    </td>
                                                    <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                                    <td>{{ $item->bulan }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">Tidak ada data ringkasan barang
                                                        keluar untuk bulan {{ $namaBulan[$bulan] }} {{ $tahun }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        @if (count($barangKeluarBulanan) > 0)
                                            <tfoot>
                                                <tr class="bg-light font-weight-bold">
                                                    <td colspan="3" class="text-right">Total Keseluruhan:</td>
                                                    <td>{{ $totalJumlah }}</td>
                                                    <td>{{ $totalRataPerHari }}/hari</td>
                                                    <td>Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        @endif
                                    </table>
                                </div>

                                <!-- Pagination untuk ringkasan -->
                                @if ($barangKeluarBulanan->hasPages())
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <div class="dataTables_info">
                                                <small class="text-muted">
                                                    Menampilkan {{ $barangKeluarBulanan->firstItem() }} sampai
                                                    {{ $barangKeluarBulanan->lastItem() }}
                                                    dari {{ $barangKeluarBulanan->total() }} jenis barang
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="dataTables_paginate paging_simple_numbers float-right">
                                                <nav aria-label="Pagination Navigation">
                                                    <ul class="pagination pagination-sm justify-content-end mb-0">
                                                        {{-- Previous Page Link --}}
                                                        @if ($barangKeluarBulanan->onFirstPage())
                                                            <li class="page-item disabled">
                                                                <span class="page-link">
                                                                    <i class="fas fa-angle-left"></i>
                                                                </span>
                                                            </li>
                                                        @else
                                                            <li class="page-item">
                                                                <a class="page-link"
                                                                    href="{{ $barangKeluarBulanan->previousPageUrl() }}">
                                                                    <i class="fas fa-angle-left"></i>
                                                                </a>
                                                            </li>
                                                        @endif

                                                        {{-- Pagination Elements --}}
                                                        @foreach ($barangKeluarBulanan->getUrlRange(1, $barangKeluarBulanan->lastPage()) as $page => $url)
                                                            @if ($page == $barangKeluarBulanan->currentPage())
                                                                <li class="page-item active">
                                                                    <span class="page-link">{{ $page }}</span>
                                                                </li>
                                                            @else
                                                                <li class="page-item">
                                                                    <a class="page-link"
                                                                        href="{{ $url }}">{{ $page }}</a>
                                                                </li>
                                                            @endif
                                                        @endforeach

                                                        {{-- Next Page Link --}}
                                                        @if ($barangKeluarBulanan->hasMorePages())
                                                            <li class="page-item">
                                                                <a class="page-link"
                                                                    href="{{ $barangKeluarBulanan->nextPageUrl() }}">
                                                                    <i class="fas fa-angle-right"></i>
                                                                </a>
                                                            </li>
                                                        @else
                                                            <li class="page-item disabled">
                                                                <span class="page-link">
                                                                    <i class="fas fa-angle-right"></i>
                                                                </span>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Info pagination ringkasan tanpa pagination -->
                                    @if ($barangKeluarBulanan->total() > 0)
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                Menampilkan {{ $barangKeluarBulanan->total() }} jenis barang
                                            </small>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
