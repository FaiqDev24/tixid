@extends('templates.app')

@section('content')
    <div class="w-75 d-block mx-auto my-5 p-4">
        <h5 class="text-center my-3">Ubah Data Promo</h5>
        <form action="{{ route('staff.promos.update', $promo->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="promo_code" class="form-label">Kode Promo</label>
                <input type="text" class="form-control @error('promo_code') is-invalid @enderror" id="promo_code"
                    name="promo_code" value="{{ $promo['promo_code'] }}">
                @error('promo_code')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Tipe Promo</label>
                <select name="type" class="form-select">
                    <option value="percent" {{ $promo['type'] == 'percent' ? 'selected' : '' }}>Persentase
                    </option>
                    <option value="rupiah" {{ $promo['type'] == 'rupiah' ? 'selected' : '' }}>Rupiah</option>
                </select>
                @error('type')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="discount" class="form-label">Total Potongan</label>
                <input type="number" class="form-control @error('discount') is-invalid @enderror" id="discount"
                    name="discount" value="{{ $promo['discount'] }}">
                @error('discount')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Kirim</button>
        </form>
    </div>
@endsection
