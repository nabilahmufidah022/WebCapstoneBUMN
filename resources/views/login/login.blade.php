<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | RUMAH BUMN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: #f9f9f9;
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .login-left {
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .login-right {
            padding: 50px;
        }
        .form-control {
            background: #f2f2f2;
            border: none;
            border-radius: 6px;
            height: 45px;
        }
        /* Penyesuaian agar input group pas dengan desain borderless bawaan */
        .input-group-text-custom {
            background: #f2f2f2;
            border: none;
            border-radius: 0 6px 6px 0;
            color: #6c757d;
            cursor: pointer;
        }
        .input-control-custom {
            border-radius: 6px 0 0 6px !important;
        }
        .btn-login {
            background: #2f318b;
            color: #fff;
            height: 50px;
            font-size: 16px;
            border-radius: 6px;
        }
        .btn-login:hover {
            background: #23245f;
        }
        .form-check-label {
            font-size: 12px;
        }
        .extra-links {
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="row login-card w-75">

        <div class="col-md-6 login-left">
            <img src="{{ asset('img/login/ilustrasi.svg') }}" alt="Login Illustration" class="img-fluid">
        </div>

        <div class="col-md-6 login-right">

            <div class="text-center mb-4">
                <img src="{{ asset('img/login/logo.png') }}" alt="MeRapat Logo" style="height: 60px;">
            </div>

            <form method="POST" action="{{ route('logincheck') }}">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-semibold">Username</label>
                    <input type="text" name="email" class="form-control" placeholder="Username" value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control input-control-custom"
                               placeholder="Password"
                               required>
                        <span class="input-group-text input-group-text-custom px-3" id="togglePassword">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn w-100 btn-login">Login</button>

                <div class="text-center mt-3">
                    <span style="font-size: 14px;">Don't have an account?
                        <a href="{{ route('register') }}" class="text-primary fw-semibold">Register</a>
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordField = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            passwordField.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    });
</script>
</body>
</html>
