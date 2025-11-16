@extends('templates.app')
@section('content')
    <div class="container my-5">
        <h5 class="mb-5">Seluruh film sedang tayang</h5>
        <form action="" class="row mb-3" method="GET">
            @csrf
            <div class="col-10">
                <input type="text" name="search_movie" placeholder="Cari judul film.." class="form-control">
            </div>
            <div class="col-2">
                <button type="submit" class="btn btn-primary">Cari</button>
            </div>
        </form>
        <div class="container d-flex gap-3 mt-4 justify-content-center">
       @foreach ($movies as $key => $item)
            <div class="card" style="width: 18rem;">
                <img src="{{ asset('storage/' . $item['poster']) }}" class="card-img-top" alt="{{ $item['title'] }}"
                    style="height: 350px; object-fit: cover;">
                <div class="card-body bg-warning" style="padding: 0 !important; text-align: center;">
                    {{-- Karna default card text ad paddingnya, biar paddingnya yang dibaca dari style jadi dikasi !important (memprioritaskan style) --}}
                    <p class="card-text" style="padding: 0 !important; text-align: center; font-weight: bold;"><a
                        href="{{ route('schedules.detail', ['movie_id' => $item['id']]) }}">BELI TIKET</a></p>
                </div>
            </div>
        @endforeach
    </div>
    </div>
@endsection
