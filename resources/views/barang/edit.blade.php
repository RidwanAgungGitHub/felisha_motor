<!-- edit.blade.php (save in resources/views/barang/edit.blade.php) -->
<div class="modal fade" id="editBarangModal{{ $barang->id }}" tabindex="-1"
    aria-labelledby="editBarangModal{{ $barang->id }}Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBarangModal{{ $barang->id }}Label">Edit Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('barang.update', $barang->id) }}" method="POST">
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_nama_barang{{ $barang->id }}" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="edit_nama_barang{{ $barang->id }}"
                            name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_merek{{ $barang->id }}" class="form-label">Merek</label>
                        <input type="text" class="form-control" id="edit_merek{{ $barang->id }}" name="merek"
                            value="{{ old('merek', $barang->merek) }}" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_harga{{ $barang->id }}" class="form-label">Harga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" step="0.01" class="form-control"
                                    id="edit_harga{{ $barang->id }}" name="harga"
                                    value="{{ old('harga', $barang->harga) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_satuan{{ $barang->id }}" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="edit_satuan{{ $barang->id }}"
                                name="satuan" value="{{ old('satuan', $barang->satuan) }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_stok{{ $barang->id }}" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="edit_stok{{ $barang->id }}" name="stok"
                            value="{{ old('stok', $barang->stok) }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
