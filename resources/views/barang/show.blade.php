<!-- show.blade.php (save in resources/views/barang/show.blade.php) -->
<div class="modal fade" id="showBarangModal{{ $barang->id }}" tabindex="-1"
    aria-labelledby="showBarangModal{{ $barang->id }}Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showBarangModal{{ $barang->id }}Label">Detail Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th width="35%">ID</th>
                            <td>{{ $barang->id }}</td>
                        </tr>
                        <tr>
                            <th>Nama Barang</th>
                            <td>{{ $barang->nama_barang }}</td>
                        </tr>
                        <tr>
                            <th>Merek</th>
                            <td>{{ $barang->merek }}</td>
                        </tr>
                        <tr>
                            <th>Harga</th>
                            <td>Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Satuan</th>
                            <td>{{ $barang->satuan }}</td>
                        </tr>
                        <tr>
                            <th>Stok</th>
                            <td>
                                @if ($barang->stok < 10)
                                    <span class="badge bg-danger">{{ $barang->stok }}</span>
                                @else
                                    {{ $barang->stok }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td>{{ $barang->created_at->format('d-m-Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diperbarui</th>
                            <td>{{ $barang->updated_at->format('d-m-Y H:i:s') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#editBarangModal{{ $barang->id }}" data-bs-dismiss="modal">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <form action="{{ route('barang.destroy', $barang->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
