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
                                <button type="submit" class="btn btn-primary" name="tab"
                                    value="{{ $activeTab ?? 'histori' }}">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('barang-keluar.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-sync"></i> Reset
                                </a>
                                <a href="{{ route('kasir') }}" class="btn btn-success">
                                    <i class="fas fa-cash-register"></i> Mode Kasir
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

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
                                                    <td>{{ $index + 1 }}</td>
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
                            </div>

                            <!-- Tab untuk ringkasan bulanan barang keluar (yang perlu diperbaiki) -->
                            <div class="tab-pane fade {{ $activeTab == 'ringkasan' ? 'show active' : '' }}" id="ringkasan"
                                role="tabpanel" aria-labelledby="ringkasan-tab">
                                <h5 class="mb-3">
                                    Ringkasan Barang Keluar Bulan {{ $namaBulan[$bulan] ?? '' }} {{ $tahun }}
                                    <small class="text-muted">(Total {{ $totalHariAktif }} hari aktif)</small>
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
                                                    <td>{{ $index + 1 }}</td>
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
                                                    <td colspan="3" class="text-right">Total:</td>
                                                    <td>{{ $totalJumlah }}</td>
                                                    <td>{{ $totalRataPerHari }}/hari</td>
                                                    <td>Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        @endif
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
