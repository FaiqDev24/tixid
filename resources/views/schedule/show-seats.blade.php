@extends('templates.app')

@section('content')
    <div class="container card my-5 pt-4" style="margin-bottom: 10% !important;">
        <div class="card-body">
            <b>{{ $schedule['cinema']['name'] }}</b>
            <br>
            <b>{{ now()->format('d F, Y') }} - {{ $hour }}</b>
        </div>
        <div class="alert alert-secondary">
            <i class="fa-solid fa-info text-danger me-3"></i>Anak usia 2 tahun keatas wajib membeli tiket.
        </div>
        <div class="d-block mx-auto my-4">
            <div class="row mb-3">
                <div class="col-4 d-flex justify-content-center">
                    <div style="background: #112646; width: 20px; height: 20px;"></div>
                    <span class="ms-2">Kursi Tersedia</span>
                </div>
                <div class="col-4 d-flex justify-content-center">
                    <div style="background: #2881daff; width: 20px; height: 20px;"></div>
                    <span class="ms-2">Kursi Dipilih</span>
                </div>
                <div class="col-4 d-flex justify-content-center">
                    <div style="background: #eaeaea; width: 20px; height: 20px;"></div>
                    <span class="ms-2">Kursi Terjual</span>
                </div>
            </div>

            @php
                //membuat array dengan rentan tertentu range()
                $rows = range('A', 'H');
                $cols = range(1, 18);
            @endphp

            @foreach ($rows as $row)
                <div class="d-flex justify-content-center">
                    @foreach ($cols as $col)
                        @if ($col == 7)
                            <div style="width: 50px;"></div>
                        @endif
                        @php
                            $seat = $row . "-" . $col;
                        @endphp
                        {{-- Cek apakah di array $seatsFormat ada daa kursi ini : in_array() --}}
                        @if (in_array($seat, $seatsFormat))
                            <div style="background: #eaeaea; color:black; width :40px; height:35px; margin: 5px; border-radius: 5px; text-align:center; padding-top: 3px;">
                            <small><b>{{ $row }}-{{ $col }}</b></small>
                            </div>
                        @else
                            <div style="background: #112646; color:white; width :40px; height:35px; margin: 5px; border-radius: 5px; text-align:center; padding-top: 3px;"
                            onclick="selectSeat('{{ $schedule->price }}', '{{ $row }}', '{{ $col }}', this)">
                            <small><b>{{ $row }}-{{ $col }}</b></small>
                        </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <div class="fixed-bottom">
        <div class="w-100 bg-light text-center py-3" style="border: 1px solid #747474ff;">
            <b>LAYAR BIOSKOP</b>
        </div>
        <div class="row">
            <div class="col-6 bg-light text-center p3" style="border: 1px solid #747474ff;">
                <b>Total Harga</b>
                <br><b id="totalPrice">Rp. -</b>
            </div>
            <div class="col-6 bg-light text-center p3" style="border: 1px solid #747474ff;">
                <b>Kursi Dipilih</b>
                <br><b id="selectedSeat">-</b>
            </div>
        </div>
        <!-- input:hidden menyembunyikan konten html, digunakan untuk menyimpan nilai php di js -->
        <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
        <input type="hidden" name="schedule_id" id="schedule_id" value="{{ $schedule->id }}">
        <input type="hidden" name="hour_id" id="hour_id" value="{{ $hour }}">

        <div class="w-100 bg-light text-center py-3" style="font-weight: bold;" id="btnCreateOrder">
            RINGKASAN ORDER
        </div>
    </div>
@endsection

@push('script')
    <script>
        let seats = []
        let totalPrice = 0

        function selectSeat(price, row, col, element) {
            // buat format A-1
            let seat = row + "-" + col
            let indexSeat = seats.indexOf(seat)

            if (indexSeat == -1) {
                seats.push(seat)
                element.style.background = '#2881daff'
            } else {
                seats.splice(indexSeat, 1)
                element.style.background = '#112646'
            }

            totalPrice = price * seats.length
            let totalPriceElement = document.querySelector("#totalPrice")
            totalPriceElement.innerText = "Rp. " + totalPrice

            let selectedSeatElement = document.querySelector("#selectedSeat")
            selectedSeatElement.innerText = seats.join(', ')

            let btnCreateOrder = document.querySelector("#btnCreateOrder")
            if (seats.length > 0) {
                btnCreateOrder.style.background = '#112646'
                btnCreateOrder.style.color = 'white'
                btnCreateOrder.classList.remove("bg-light")
                btnCreateOrder.onclick = createOrder
            } else {
                btnCreateOrder.style.background = ''
                btnCreateOrder.style.color = ''
                btnCreateOrder.onclick = null
            }
        }

        function createOrder() {
            let data = {
                user_id: $("#user_id").val(),
                schedule_id: $("#schedule_id").val(),
                rows_of_seats: seats,
                quantity: seats.length,
                total_price: totalPrice,
                tax: 4000 * seats.length,
                hour_id: $("#hour_id").val(),
                _token: "{{  csrf_token() }}", // csrf token
            }

            $.ajax({
                url: "{{ route('tickets.store') }}",
                method: "POST",
                data: data,
                success: function(response) {
                    // kalo berhasil mau ngapain
                    let ticketId = response.data.id;
                    //pindah halamn : window.location.href
                    window.location.href = `/tickets/${ticketId}/order`;
                },
                error: function(message) {
                    // kalau gagal mau ngapain
                    alert('Gagal membuat data tiket!')
                }
            })
        }
    </script>
