@extends('templates.app')

@section('content')
<div class="container my-4">
    <h4>Tambah Promo</h4>
    <form action="{{ route('staff.promos.store') }}" method="POST">
        @csrf

        {{-- Kode Promo --}}
        <div class="mb-3">
            <label for="promo_code" class="form-label">Kode Promo</label>
            <input type="text" name="promo_code" id="promo_code"
                   class="form-control @error('promo_code') is-invalid @enderror"
                   >
            @error('promo_code')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Tipe Promo --}}
        <div class="mb-3">
            <label for="type" class="form-label">Tipe Promo</label>
            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                <option value="">Pilih</option>
                <option value="percent" {{ old('type') == 'percent' ? 'selected' : '' }}>%</option>
                <option value="rupiah" {{ old('type') == 'rupiah' ? 'selected' : '' }}>Rupiah</option>
            </select>
            @error('type')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Jumlah Potongan --}}
        <div class="mb-3">
            <label for="discount" class="form-label">Jumlah Potongan</label>
            <input type="number" name="discount" id="discount"
                   class="form-control @error('discount') is-invalid @enderror"
                   value="{{ old('discount') }}">
            @error('discount')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Tombol --}}
        <button type="submit" class="btn btn-primary">Kirim</button>
    </form>
</div>
@endsection
