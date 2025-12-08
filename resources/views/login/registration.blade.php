<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            
            <!-- Left Illustration -->
            <div class="col-md-6 login-left">
                <img src="{{ asset('img/login/ilustrasi.svg') }}" alt="Login Illustration" class="img-fluid">
            </div>

            <!-- Right Login Form -->
            <div class="col-md-6 login-right">
                
                <!-- Logo -->
                <div class="text-center mb-4">
                    <img src="{{ asset('img/login/logo.png') }}" alt="MeRapat Logo" style="height: 60px;">
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('registercheck') }}">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Username -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter Your Name" value="{{ old('name') }}" required>
                    </div>

                    <!-- Username -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="text" name="email" class="form-control" placeholder="Username" value="{{ old('email') }}" required>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>

                    <!-- Button -->
                    <button type="submit" class="btn w-100 btn-login">Register</button>

                    <div class="text-center mt-3">
                        <span style="font-size: 14px;">Already have an account?
                            <a href="{{ route('login') }}" class="text-primary fw-semibold">Login</a>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
