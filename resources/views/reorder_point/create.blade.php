<!-- resources/views/reorder_point/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Tambah Reorder Point</h5>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('reorder-point.store') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="barang_id">Nama Barang</label>
                                <select class="form-control select2" id="barang_id" name="barang_id" required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach ($barangs as $barang)
                                        <option value="{{ $barang->id }}">{{ $barang->nama_barang }} -
                                            {{ $barang->merek }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="safety_stock">Safety Stock</label>
                                <input type="number" class="form-control" id="safety_stock" name="safety_stock"
                                    value="{{ old('safety_stock') }}" required readonly>
                                <small class="form-text text-muted">Nilai Safety Stock dari perhitungan terbaru</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="lead_time">Lead Time (hari)</label>
                                <input type="number" class="form-control" id="lead_time" name="lead_time"
                                    value="{{ old('lead_time') }}" required>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('reorder-point.index') }}" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize select2
            $('.select2').select2({
                placeholder: "-- Pilih Barang --",
                width: '100%'
            });

            // Get safety stock when barang is selected
            $('#barang_id').on('change', function() {
                const barangId = $(this).val();
                if (barangId) {
                    $.ajax({
                        url: "{{ route('reorder-point.get-safety-stock') }}",
                        type: "GET",
                        data: {
                            barang_id: barangId
                        },
                        success: function(data) {
                            $('#safety_stock').val(data.safety_stock || 0);
                        },
                        error: function() {
                            alert('Terjadi kesalahan saat mengambil data safety stock');
                        }
                    });
                } else {
                    $('#safety_stock').val('');
                }
            });
        });
    </script>
@endpush
