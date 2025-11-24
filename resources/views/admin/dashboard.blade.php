@extends('templates.app')

@section('content')
    <div class="container mt-5">
        <h5>Grafik Pembelian Tiket</h5>
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }} <b>Selamat Datang, {{ Auth::user()->name }}</b>
            </div>
        @endif
        <div class="row">
            <div class="col-6">
                <h5>Data Pembelian tiket bulan {{ now()->format('F') }}</h5>
                <canvas id="chartBar"></canvas>
            </div>
            <div class="col-6">
                <h5>Perbandingan film AKtif & Non-Aktif</h5>
                <div style="width: 450px">
                    <canvas id="chartPie" style="width: 50%;"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(function() {
            // ajax dipanggil ketika halaman baru selesai di refresh
            let lablesBar = [];
            let dataBar = [];
            $.ajax({
                url: "{{ route('admin.tickets.chart') }}",
                method: "GET",
                success: function(response) {
                    lablesBar = response.lables; // var lablesBar dari controller json bagian lables
                    dataBar = response.data;
                    // fungsi kondigurasi chart
                    showChartBar();
                },
                error: function(err) {
                    alert('Gagal mengambil data chart tiket!');
                }
            });

            let dataPie = [];
            $.ajax({
                url: "{{ route('admin.movies.chart') }}",
                method: "GET",
                success: function(response) {
                    dataPie = response.data;
                    showChartPie();
                },
                error: function(err) {
                    alert('Gagal mengambil data chart film!');
                }
            })

            function showChartBar() {
                const ctx = document.getElementById('chartBar');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: lablesBar,
                        datasets: [{
                            label: '# of Votes',
                            data: dataBar,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            function showChartPie() {
                const ctx2 = document.getElementById('chartPie');

                new Chart(ctx2, {
                    type: 'pie',
                    data: {
                        labels: [
                            'Aktif',
                            'Non-Aktif',
                        ],
                        datasets: [{
                            label: 'Perbandingan film AKtif & Non-Aktif',
                            data: dataPie,
                            backgroundColor: [
                                'rgb(255, 99, 132)',
                                'rgb(54, 162, 235)',
                            ],
                            hoverOffset: 4
                        }]
                    }
                })
            }
        })
    </script>
@endpush
