<!-- Create Barang Masuk Modal -->
<div class="modal fade" id="createBarangMasukModal" tabindex="-1" aria-labelledby="createBarangMasukModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createBarangMasukModalLabel">Tambah Barang Masuk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('barang_masuk.store') }}" method="POST" id="createBarangMasukForm">
                    @csrf
                    <div class="mb-3">
                        <label for="barang_id" class="form-label">Pilih Barang</label>
                        <select class="form-select" id="barang_id" name="barang_id" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($barangs as $barang)
                                <option value="{{ $barang->id }}"
                                    {{ $selectedBarang && $selectedBarang->id == $barang->id ? 'selected' : '' }}>
                                    {{ $barang->nama_barang }} - {{ $barang->merek }} (Stok: {{ $barang->stok }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">Pilih Supplier</label>
                        <div class="input-group">
                            <select class="form-select" id="supplier_id" name="supplier_id" required>
                                <option value="">-- Pilih Supplier --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->nama }} - {{ $supplier->kontak }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-secondary"
                                onclick="window.open('{{ route('supplier.create') }}', '_blank')">
                                <i class="fas fa-plus"></i> Tambah Supplier
                            </button>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="harga" class="form-label">Harga Per Unit</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" step="0.01" class="form-control" id="harga" name="harga"
                                    value="{{ old('harga') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah"
                                value="{{ old('jumlah') }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tanggal_beli" class="form-label">Tanggal Pembelian</label>
                            <input type="date" class="form-control" id="tanggal_beli" name="tanggal_beli"
                                value="{{ old('tanggal_beli', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="estimasi_datang" class="form-label">Estimasi Kedatangan</label>
                            <input type="date" class="form-control" id="estimasi_datang" name="estimasi_datang"
                                value="{{ old('estimasi_datang', date('Y-m-d', strtotime('+7 days'))) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending (Belum
                                Diterima)</option>
                            <option value="diterima" {{ old('status') == 'diterima' ? 'selected' : '' }}>Diterima
                                (Langsung
                                Update Stok)</option>
                        </select>
                        <small class="text-muted">Pilih "Diterima" jika barang sudah datang dan siap untuk menambah
                            stok.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="createBarangMasukForm" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to refresh supplier data -->
<script>
    $(document).ready(function() {
        // Function to refresh supplier dropdown
        function refreshSupplierDropdown() {
            $.ajax({
                url: "{{ route('api.supplier') }}",
                type: "GET",
                dataType: "json",
                success: function(data) {
                    let dropdown = $('#supplier_id');
                    dropdown.empty();
                    dropdown.append('<option value="">-- Pilih Supplier --</option>');

                    $.each(data, function(key, supplier) {
                        dropdown.append(
                            $('<option></option>')
                            .attr('value', supplier.id)
                            .text(supplier.nama + ' - ' + supplier.kontak)
                        );
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching suppliers: " + error);
                }
            });
        }

        // Refresh when the window gets focus (in case a new supplier was added in another tab)
        $(window).focus(function() {
            refreshSupplierDropdown();
        });

        // Manual refresh button in case the automatic refresh doesn't work
        $('#createBarangMasukModal').on('shown.bs.modal', function() {
            refreshSupplierDropdown();
        });
    });
</script>
