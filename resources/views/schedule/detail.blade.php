@extends('templates.app')

@section('content')
    <div class="container pt-5">
        <div class="w-75 d-block m-auto">
            <div class="d-flex">
                <div style="width: 200px; height: 200px">
                    <img src="{{ asset('storage/' . $movie['poster']) }}" alt="" class="w-100"
                        style="border-radius: 5px">
                </div>
                <div class="ms-5 mt-4">
                    <h5>{{ $movie['title'] }}</h5>
                    <table>
                        <tr>
                            <td><b class="text-secondary">Genre</b></td>
                            <td class="px-3"></td>
                            <td>{{ $movie['genre'] }}</td>
                        </tr>
                        <tr>
                            <td><b class="text-secondary">Duration</b></td>
                            <td class="px-3"></td>
                            <td>{{ $movie['duration'] }}</td>
                        </tr>
                        <tr>
                            <td><b class="text-secondary">Sutradara</b></td>
                            <td class="px-3"></td>
                            <td>{{ $movie['director'] }}</td>
                        </tr>
                        <tr>
                            <td><b class="text-secondary">Rating Usia</b></td>
                            <td class="px-3"></td>
                            <td><span class="badge badge-danger">{{ $movie['age_rating'] }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="w-100 row mt-5">
                <div class="col-6 pe-5">
                    <div class="d-flex flex-column justify-content-end align-items-end">
                        <div class="d-flex align-items-center">
                            <h3 class="text-warning me-2">6.8</h3>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star-half-alt text-warning"></i>
                            <i class="fas fa-star" style="color: transparent; -webkit-text-stroke: 1px #dda500;"></i>
                        </div>
                        <small>16.736 Vote</small>
                    </div>
                </div>
                <div class="col-6 ps-5" style="border-left: 2px solid #c7c7c7;">
                    <div class="d-flex align-items-center">
                        <div class="fas fa-heart text-danger me-2"></div>
                        <b>Masukan Watchlist</b>
                    </div>
                    <small>10.000 Orang</small>
                </div>
            </div>
            <div class="d-flex w-100 bg-light mt-3">
                <div class="dropdown">
                    <button class="btn btn-light w-100 text-start dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-mdb-dropdown-init data-mdb-ripple-init aria-expanded="false">
                        BIOSKOP
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @foreach ($movie['schedules'] as $schedule)
                            <li><a class="dropdown-item" href="#">{{ $schedule['cinema']['name'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                @php
                    // request()->get('name_query') : memanggil query params (?) di url
                    // jika ? nilainya ASC ubah jadi DESC
                    if (request()->get('sort_price') == 'ASC') {
                        $sortPrice = 'DESC';
                    } elseif (request()->get('sort_price') == 'DESC') {
                        // jika query DESC ubah jadi ASC
                        $sortPrice = 'ASC';
                    } else {
                        // pertama kali di klik ASC
                        $sortPrice = 'ASC';
                    }

                    if (request()->get('sort_alfabet') == 'ASC') {
                        $sortAlfabet = 'DESC';
                    } elseif (request()->get('sort_alfabet') == 'DESC') {
                        // jika query DESC ubah jadi ASC
                        $sortAlfabet = 'ASC';
                    } else {
                        // pertama kali di klik ASC
                        $sortAlfabet = 'ASC';
                    }
                @endphp
                <div class="dropdown">
                    <button class="btn btn-light w-100 text-start dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-mdb-dropdown-init data-mdb-ripple-init aria-expanded="false">
                        SORTIR
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="?sort_price={{ $sortPrice }}">Harga</a></li>
                        <li><a class="dropdown-item" href="?sort_alfabet={{ $sortAlfabet }}">Alfabet</a></li>
                    </ul>
                </div>
            </div>
            <div class="mb-5">
                @foreach ($movie['schedules'] as $schedule)
                    <div class="w-100 my-3">
                        <div class="d-flex justify-content-between">
                            {{-- kanan --}}
                            <div>
                                <i class="fa-solid fa-building"><b class="ms-2">{{ $schedule['cinema']['name'] }}</b></i>
                                <br>
                                <small class="ms-3">{{ $schedule['cinema']['location'] }}</small>
                            </div>
                            {{-- kiri --}}
                            <div>
                                <b>Rp. {{ number_format($schedule['price'], 0, ',', '.') }}</b>
                            </div>
                        </div>

                        <div class="d-flex gap-3 ps-3 my-2">
                            {{--  hours berbentuk array, sehingga gunakan loop untuk akses itemnya --}}
                            @foreach ($schedule['hours'] as $index => $hours)
                                {{--
                                    1. schedule->id : mengambil detail schedule
                                    2. index : untuk mengambil index dari array hours untuk mengetahui jam berapa tiket akan dipesan
                                    3. this : mengambil elemen html yang diklik secara penuh untuk diakses di JavaScript
                                --}}
                                <div class="btn btn-outline-secondary" style="cursor: pointer"
                                    onclick="selectedHour('{{ $schedule->id }}', '{{ $index }}', this)">
                                    {{ $hours }}</div>
                            @endforeach
                        </div>
                        <hr>
                @endforeach
            </div>
            <div class="w-100 p-2 bg-light text-center fixed-bottom">
                <a href=""><i class="fa-solid fa-ticket"> BELI TIKET</i></a>
            </div>
        </div>
    </div>
@endsection

@push('script')

<script>
    let selectedScheduleId = null;
    let selectedHourIndex = null;
    let lastClicked = null;

    function selectedHour(scheduleId, hourIndex, el) {
        selectedScheduleIndex = scheduleId;
        selectedHourIndex = hourIndex;

        if(lastClicked) {
            lastClicked.style.backgroundColor = "";
            lastClicked.style.color = "";
            lastClicked.style.borderColor = "";
        }

        // ubah warna kotak yang di klik
        // el diambil dari parameter fungsi dengan nilai argumen this di HTML nya
        el.style.backgroundColor = "#112646";
        el.style.color = "white";
        el.style.borderColor = "112646";

        lastClicked = el;

        let wrapBtn = document.querySelector("#wrapBtn")
        wrapBtn.classList.remove("bg-light")
        wrapBtn.style.backgroundColor = "#112646"

        let url = "{{ route('schedules.show-seats', ['scheduleId' => ':scheduleId', 'hourId' => ':hourId']) }}"
            .replace(':scheduleId', scheduleId)
            .replace(':hourId', hourIndex)

        let btnOrder = document.querySelector("#btnOrder")
        btnOrder.href = url
        btnOrder.style.color = 'white'
    }
</script>

@endpush
