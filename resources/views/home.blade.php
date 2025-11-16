@extends('templates.app')

@section('content')
    @if (Session::get('success'))
        <div class="alert alert-success">{{ Session::get('success') }} <b>Selamat Datang, {{ Auth::user()->name }}</b>
        </div>
        {{--Auth::user()->name : kata name diambil dari model user - fillable --}}
    @endif
    @if (Session::get('logout'))
        <div class="alert alert-warning">{{ Session::get('logout') }}</div>
    @endif
    <div class="dropdown">
        <button class="btn btn-light w-100 text-start dropdown-toggle" type="button" id="dropdownMenuButton"
            data-mdb-dropdown-init data-mdb-ripple-init aria-expanded="false">
            <i class="fa-solid fa-location-dot"> Bogor</i>
        </button>
        <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
            <li><a class="dropdown-item" href="#">Jakarta Timur</a></li>
            <li><a class="dropdown-item" href="#">Jakarta Barat</a></li>
            <li><a class="dropdown-item" href="#">Depok</a></li>
        </ul>
    </div>
    <!-- Carousel wrapper -->
    <div id="carouselBasicExample" class="carousel slide carousel-fade" data-mdb-ride="carousel" data-mdb-carousel-init>
        <!-- Indicators -->
        <div class="carousel-indicators">
            <button type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide-to="0" class="active"
                aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide-to="1"
                aria-label="Slide 2"></button>
            <button type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide-to="2"
                aria-label="Slide 3"></button>
        </div>

        <!-- Inner -->
        <div class="carousel-inner">
            <!-- Single item -->
            <div class="carousel-item active">
                <img style="height: 540px;"
                    src="https://kabarterdepan.com/wp-content/uploads/2025/06/5a086b9c2af14a3191c850a73740a688.jpg"
                    class="d-block w-100" alt="Sunset Over the City" />
                {{-- <div class="carousel-caption d-none d-md-block">
                    <h5>First slide label</h5>
                    <p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p>
                </div> --}}
            </div>

            <!-- Single item -->
            <div class="carousel-item">
                <img style="height: 540px;" src="https://anandastoon.com/wp-content/uploads/2023/07/header.jpg"
                    class="d-block w-100" alt="Canyon at Nigh" />
            </div>

            <!-- Single item -->
            <div class="carousel-item">
                <img style="height: 540px;"
                    src="https://wallpapers.com/images/hd/boboiboy-and-friends-the-movie-poster-rqp7rs848yvt3ni8.jpg"
                    class="d-block w-100" alt="Cliff Above a Stormy Sea" />
            </div>
        </div>
        <!-- Inner -->

        <!-- Controls -->
        <button class="carousel-control-prev" type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    <!-- Carousel wrapper -->

    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center">
            <!--konten kiri-->
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-clapperboard"></i>
                <h5 class="ms-2 mt-2">Sedang Tayang</h5>
            </div>
            <!--konten kanan-->
            <div>
                <a href="{{ route('home.movies.all') }}" class="btn btn-warning rounded-pill">Semua</a>
            </div>
        </div>
    </div>
    <div class="container d-flex gap-2">
        <!--gap 2 jarak antar komponen-->
        <a href="{{ route('home.movies.all') }}" class="btn btn-outline-primary rounded-pill">Semua Film</a>
        <a href="#" class="btn btn-outline-secondary rounded-pill">XXI</a>
        <a href="#" class="btn btn-outline-secondary rounded-pill">Cinepolis</a>
        <a href="#" class="btn btn-outline-secondary rounded-pill">Imax</a>
    </div>
    <div class="container d-flex gap-3 mt-4 justify-content-center">
       @foreach ($movies as $key => $item)
            <div class="card" style="width: 18rem;">
                <img src="{{ asset('storage/' . $item['poster']) }}" class="card-img-top" alt="{{ $item['title'] }}"
                    style="height: 350px; object-fit: cover;">
                <div class="card-body bg-warning" style="padding: 0 !important; text-align: center;">
                    {{-- Karna default card text ad paddingnya, biar paddingnya yang dibaca dari style jadi dikasi !important (memprioritaskan style) --}}
                    <p class="card-text" style="padding: 0 !important; text-align: center; font-weight: bold;"><a
                        href="{{ route('schedules.detail', $item['id']) }}">BELI TIKET</a></p>
                </div>
            </div>
        @endforeach
    </div>
    <footer class="bg-body-tertiary text-center text-lg-start mt-5">
  <!-- Copyright -->
  <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.05);">
    © 2020 Copyright:
    <a class="text-body" href="https://mdbootstrap.com/">TIXID.com</a>
  </div>
  <!-- Copyright -->
</footer>
@endsection
