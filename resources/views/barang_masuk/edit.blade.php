<!-- edit.blade.php (save in resources/views/barang_masuk/edit.blade.php) -->
<div class="modal fade" id="editBarangMasukModal{{ $barangMasuk->id }}" tabindex="-1"
    aria-labelledby="editBarangMasukModal{{ $barangMasuk->id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBarangMasukModal{{ $barangMasuk->id }}Label">Edit Barang Masuk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('barang_masuk.update', $barangMasuk->id) }}" method="POST"
                id="editBarangMasukForm{{ $barangMasuk->id }}">
                <div class="modal-body">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="barang_id{{ $barangMasuk->id }}" class="form-label">Barang</label>
                                <select class="form-select" id="barang_id{{ $barangMasuk->id }}" name="barang_id"
                                    required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach ($barangs as $barang)
                                        <option value="{{ $barang->id }}"
                                            {{ $barangMasuk->barang_id == $barang->id ? 'selected' : '' }}>
                                            {{ $barang->nama_barang }} - {{ $barang->merek }} (Stok:
                                            {{ $barang->stok }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="supplier_id{{ $barangMasuk->id }}" class="form-label">Supplier</label>
                                <select class="form-select" id="supplier_id{{ $barangMasuk->id }}" name="supplier_id"
                                    required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ $barangMasuk->supplier_id == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->nama }} - {{ $supplier->kontak }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="jumlah{{ $barangMasuk->id }}" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah{{ $barangMasuk->id }}"
                                    name="jumlah" value="{{ old('jumlah', $barangMasuk->jumlah) }}" required
                                    min="1">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="harga{{ $barangMasuk->id }}" class="form-label">Harga Satuan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="harga{{ $barangMasuk->id }}"
                                        name="harga" value="{{ old('harga', $barangMasuk->harga) }}" required
                                        min="0">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="total_preview{{ $barangMasuk->id }}" class="form-label">Total Harga</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <div class="form-control bg-light" id="total_preview{{ $barangMasuk->id }}">
                                        {{ number_format($barangMasuk->jumlah * $barangMasuk->harga, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="tanggal_beli{{ $barangMasuk->id }}" class="form-label">Tanggal
                                    Pembelian</label>
                                <input type="date" class="form-control" id="tanggal_beli{{ $barangMasuk->id }}"
                                    name="tanggal_beli" value="{{ old('tanggal_beli', $barangMasuk->tanggal_beli) }}"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="estimasi_datang{{ $barangMasuk->id }}" class="form-label">Estimasi
                                    Kedatangan</label>
                                <input type="date" class="form-control" id="estimasi_datang{{ $barangMasuk->id }}"
                                    name="estimasi_datang"
                                    value="{{ old('estimasi_datang', $barangMasuk->estimasi_datang) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="status{{ $barangMasuk->id }}" class="form-label">Status</label>
                                <select class="form-select" id="status{{ $barangMasuk->id }}" name="status" required>
                                    <option value="pending" {{ $barangMasuk->status == 'pending' ? 'selected' : '' }}>
                                        Pending (Belum Diterima)
                                    </option>
                                    <option value="diterima"
                                        {{ $barangMasuk->status == 'diterima' ? 'selected' : '' }}>
                                        Diterima (Update Stok)
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <strong>Catatan:</strong>
                        <ul class="mb-0">
                            <li>Mengubah status dari "Pending" ke "Diterima" akan menambahkan stok barang.</li>
                            <li>Mengubah status dari "Diterima" ke "Pending" akan mengurangi stok barang.</li>
                            <li>Mengubah jumlah barang dengan status "Diterima" akan menyesuaikan stok barang.</li>
                        </ul>
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
