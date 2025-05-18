<!-- show.blade.php (save in resources/views/barang_masuk/show.blade.php) -->
<div class="modal fade" id="showBarangMasukModal{{ $barangMasuk->id }}" tabindex="-1"
    aria-labelledby="showBarangMasukModal{{ $barangMasuk->id }}Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showBarangMasukModal{{ $barangMasuk->id }}Label">Detail Barang Masuk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="35%">Nama Barang</th>
                        <td>{{ $barangMasuk->barang->nama_barang }}</td>
                    </tr>
                    <tr>
                        <th>Merek</th>
                        <td>{{ $barangMasuk->barang->merek }}</td>
                    </tr>
                    <tr>
                        <th>Supplier</th>
                        <td>
                            @if ($barangMasuk->supplier)
                                {{ $barangMasuk->supplier->nama }} ({{ $barangMasuk->supplier->kontak }})
                            @else
                                <span class="text-danger">Supplier tidak tersedia</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Jumlah</th>
                        <td>{{ $barangMasuk->jumlah }}</td>
                    </tr>
                    <tr>
                        <th>Harga Satuan</th>
                        <td>Rp {{ number_format($barangMasuk->harga, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Total Harga</th>
                        <td>Rp {{ number_format($barangMasuk->jumlah * $barangMasuk->harga, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Pembelian</th>
                        <td>{{ date('d/m/Y', strtotime($barangMasuk->tanggal_beli)) }}</td>
                    </tr>
                    <tr>
                        <th>Estimasi Kedatangan</th>
                        <td>{{ date('d/m/Y', strtotime($barangMasuk->estimasi_datang)) }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if ($barangMasuk->status == 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($barangMasuk->status == 'diterima')
                                <span class="badge bg-success">Diterima</span>
                            @else
                                <span
                                    class="badge bg-secondary">{{ $barangMasuk->status ?? 'Tidak Ada Status' }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#editBarangMasukModal{{ $barangMasuk->id }}" data-bs-dismiss="modal">
                    <i class="fas fa-edit"></i> Edit
                </button>
            </div>
        </div>
    </div>
</div>
