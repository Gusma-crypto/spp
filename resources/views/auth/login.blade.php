<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="{{ asset('assets/static/images/favicon.ico') }}" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <title>SPP Payment Gateway | Login</title>
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            max-width: 100%;
        }

        .login-container {
            display: flex;
            min-height: 100vh;
            align-items: center;
            flex-direction: column;
            justify-content: center;
            background: #0088c1 url("/assets/static/images/login-background.png") 0 0/cover no-repeat;
        }
    </style>
</head>

<body>
    <div class="login-container">
        @if ($errors->any())
            <div class="alert alert-danger my-4" role="alert">
                @foreach ($errors->all() as $error)
                    <h5>{{ $error }}</h5>
                @endforeach
            </div>
        @endif

        <div class="card shadow-md mx-auto p-3" style="max-width: 400px; width: 100%;">
            <div class="card-body">
                <div class="text-center">
                    <h4 class="fw-bold">Sistem Informasi Pembayaran SPP</h4>
                </div>

                <div class="text-center">
                    <!-- <img src="{{ asset('assets/static/images/simple-logo.png') }}" alt="logo" class="img-thumbnail border-0" /> -->
                    <img  src="{{ asset('assets/logo-new.png') }}" alt="logo" class="img-thumbnail border-0 w-75" />
                </div>

                <form action="{{ route('login') }}" method="post">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email" />
                    </div>

                    <div class="mb-5">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" />
                    </div>

                    <div class="mb-3 d-grid gap-2">
                        <button type="submit" class="btn btn-outline-primary">Masuk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
</body>

</html>