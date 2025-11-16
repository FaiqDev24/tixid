<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.1.0/mdb.min.css" rel="stylesheet" />
    <link rel="icon" href="https://asset.tix.id/wp-content/uploads/2022/05/TIXID_app_icon.png">
    <title>TIXID</title>
</head>

<body>
    <form action="{{ route('signup.send_data') }}" class="w-50 d-block mx-auto my-5" method="POST">
        @if (Session::get('failed'))
            <div class="alert alert-danger my-3">{{ Session::get('failed') }}</div>
        @endif
        {{-- digunakan untuk generate token yang diperlukan server, token CSRF digunakan di semua form untuk memberikan identitas unik, dari form ke controller --}}
        @csrf
        {{-- method pada route adalah PATH selain methon GET/POST --}}
        <div class="row mb-4">
            <div class="col">
                @error('first_name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
                <div data-mdb-input-init class="form-outline">
                    <input type="text" id="form3Example1" class="form-control @error('first_name') is-invalid
                        @enderror" name="first_name" />
                    <label class="form-label" for="form3Example1">First name</label>
                </div>
            </div>
            <div class="col">
                @error('last_name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
                <div data-mdb-input-init class="form-outline">
                    <input type="text" id="form3Example2" class="form-control
                        @error('last_name') is-invalid @enderror" name="last_name" />
                    <label class="form-label" for="form3Example2">Last name</label>
                </div>
            </div>
        </div>
        @error('email')
            <small class="text-danger">{{ $message }}</small>
        @enderror
        <!-- Email input -->
        <div data-mdb-input-init class="form-outline mb-4">
            <input type="email" id="form3Example3" class="form-control @error('email') is-invalid @enderror"
                name="email"/>
            <label class="form-label" for="form3Example3">Email address</label>
        </div>
        @error('password')
            <small class="text-danger">{{ $message }}</small>
        @enderror
        <!-- Password input -->
        <div data-mdb-input-init class="form-outline mb-4">
            <input id="form3Example4" class="form-control @error('password') is-invalid @enderror"  type="password"
                name="password"/>
            <label class="form-label" for="form3Example4">Password</label>
        </div>

        <!-- Submit button -->
        <button data-mdb-ripple-init type="submit" class="btn btn-warning btn-block">SIGN UP</button>
        <div class="text-center mt-3">
            <a href="{{ route('home') }}" class="btn btn-link text-warning">KEMBALI</a>
        </div>
    </form>
    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.1.0/mdb.umd.min.js"></script>
</body>

</html>
