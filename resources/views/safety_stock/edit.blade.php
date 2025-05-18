<!-- resources/views/safety_stock/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Edit Safety Stock</h5>
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

                        <form action="{{ route('safety-stock.update', $safetyStock->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group mb-3">
                                <label for="barang_id">Nama Barang</label>
                                <input type="text" class="form-control"
                                    value="{{ $safetyStock->barang->nama_barang }} - {{ $safetyStock->barang->merek }}"
                                    disabled>
                                <input type="hidden" name="barang_id" value="{{ $safetyStock->barang_id }}">
                            </div>

                            <div class="form-group mb-3">
                                <label for="pemakaian_maksimum">Pemakaian Maksimum</label>
                                <input type="number" class="form-control" id="pemakaian_maksimum" name="pemakaian_maksimum"
                                    value="{{ $safetyStock->pemakaian_maksimum }}" readonly disabled>
                            </div>

                            <div class="form-group mb-3">
                                <label for="pemakaian_rata_rata">Pemakaian rata-rata perhari</label>
                                <input type="number" class="form-control" id="pemakaian_rata_rata"
                                    name="pemakaian_rata_rata" value="{{ $safetyStock->pemakaian_rata_rata }}" readonly
                                    disabled>
                            </div>

                            <div class="form-group mb-3">
                                <label for="lead_time">Lead Time (hari)</label>
                                <input type="number" class="form-control" id="lead_time" name="lead_time"
                                    value="{{ $safetyStock->lead_time }}" required>
                                <small class="form-text text-muted">Hanya lead time yang dapat diubah</small>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('safety-stock.index') }}" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
