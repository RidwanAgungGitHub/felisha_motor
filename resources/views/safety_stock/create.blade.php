<!-- resources/views/safety_stock/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Tambah Safety Stock</h5>
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

                        <form action="{{ route('safety-stock.store') }}" method="POST">
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
                                <label for="pemakaian_maksimum">Pemakaian Maksimum</label>
                                <input type="number" class="form-control" id="pemakaian_maksimum" name="pemakaian_maksimum"
                                    value="{{ old('pemakaian_maksimum') }}" required readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label for="pemakaian_rata_rata">Pemakaian rata-rata perhari</label>
                                <input type="number" class="form-control" id="pemakaian_rata_rata"
                                    name="pemakaian_rata_rata" value="{{ old('pemakaian_rata_rata') }}" required readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label for="lead_time">Lead Time (hari)</label>
                                <input type="number" class="form-control" id="lead_time" name="lead_time"
                                    value="{{ old('lead_time') }}" required>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('safety-stock.index') }}" class="btn btn-secondary">Kembali</a>
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

            // Get data when barang is selected
            $('#barang_id').on('change', function() {
                const barangId = $(this).val();
                if (barangId) {
                    $.ajax({
                        url: "{{ route('safety-stock.get-barang-data') }}",
                        type: "GET",
                        data: {
                            barang_id: barangId,
                            bulan: "{{ now()->format('m') }}",
                            tahun: "{{ now()->format('Y') }}"
                        },
                        success: function(data) {
                            $('#pemakaian_maksimum').val(data.pemakaian_maksimum || 0);
                            $('#pemakaian_rata_rata').val(data.pemakaian_rata_rata || 0);
                        },
                        error: function() {
                            alert('Terjadi kesalahan saat mengambil data barang');
                        }
                    });
                } else {
                    $('#pemakaian_maksimum').val('');
                    $('#pemakaian_rata_rata').val('');
                }
            });
        });
    </script>
@endpush
