@extends('templates.app')

@section('content')
    <div class="container my-5 card">
        <div class="card-body">
            @if (count($schedules) > 0)
                <i class="fa-solid fa-location-dot me-3"></i>{{ $schedules[0]['cinema']['location'] }}
                <hr>
                @foreach ($schedules as $schedule)
                    <div class="my-2">
                        <div class="d-flex">
                            <div style="width: 150px; height: 200px;">
                                <img src="{{ asset('storage/' . $schedule['movie']['poster']) }}" alt="" class="w-100"
                                    style="border-radius: 7px;">
                            </div>
                            <div class="ms-5 mt-4">
                                <h5 class="fw-bold">{{ $schedule['movie']['title'] }}</h5>
                                <table>
                                    <tr>
                                        <td><b class="text-secondary">Genre</b></td>
                                        <td class="px-3"></td>
                                        <td>{{ $schedule['movie']['genre'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><b class="text-secondary">Durasi</b></td>
                                        <td class="px-3"></td>
                                        <td>{{ $schedule['movie']['duration'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><b class="text-secondary">Sutradara</b></td>
                                        <td class="px-3"></td>
                                        <td>{{ $schedule['movie']['director'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><b class="text-secondary">Rating usia</b></td>
                                        <td class="px-3"></td>
                                        <td><span class="badge badge-danger">{{ $schedule['movie']['age_rating'] }}+</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="w-100 my-3">
                            <div class="d-flex justify-content-end">
                                <div>
                                    <b class="text">Rp. {{ number_format($schedule['price'], 0, ',', '.') }}</b>
                                </div>
                            </div>

                            <div class="d-flex gap-3 ps-3 my-2">
                                <!-- karena berbentuk arry, sehingga gunakan loop untuk akses item nya -->
                                @foreach ($schedule['hours'] as $index => $hour)
                                    <!--
                                                    1. schedule-?id mengambil detail schedule yang akan dibeli
                                                    2. $index mengambil index dari arry hours untuk mnegtahui jam berapa tiket akan dipesan
                                                    3. this mengambil element html yang diklik secara penuh untuk diakses js
                                                     -->
                                    <div class="btn btn-outline-secondary" style="cursor:pointer;"
                                        onclick="selectedHour('{{ $schedule->id }}', '{{ $index }}', this)">
                                        {{ $hour }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <hr>
                @endforeach
            @else
                <div class="text-center text-secondary my-5">
                    <i class="fa-solid fa-film fa-2x mb-3"></i>
                    <p>Tidak ada jadwal film untuk saat ini.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="w-100 p-2 bg-light text-center fixed-bottom" id="wrapBtn">
        <!-- javscripst void nonaktifkan href -->
        <a href="javascript:void(0)" id="btnOrder"><i class="fa-solid fa-ticket"></i> BELI TIKET</a>
    </div>
@endsection

@push('script')
    <script>
        let selectedScheduleId = null
        let selectedHourIndex = null
        let lastClicked = null

        function selectedHour(scheduleId, hourIndex, el) {
            selectedScheduleId = scheduleId
            selectedHourIndex = hourIndex

            if (lastClicked) {
                lastClicked.style.backgroundColor = ""
                lastClicked.style.color = ""
                lastClicked.style.borderColor = ""
            }

            el.style.backgroundColor = "#112646"
            el.style.color = "white"
            el.style.borderColor = "#112646"

            lastClicked = el

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
