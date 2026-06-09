<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | RUMAH BUMN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: #f9f9f9;
        }
        .reset-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 450px;
            width: 100%;
        }
        .form-control {
            background: #f2f2f2;
            border: none;
            border-radius: 6px;
            height: 45px;
        }
        .btn-reset {
            background: #2f318b;
            color: #fff;
            height: 50px;
            font-size: 16px;
            border-radius: 6px;
        }
        .btn-reset:hover {
            background: #23245f;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="reset-card">
        
        <div class="text-center mb-4">
            <h5 class="fw-bold text-dark">Reset Password</h5>
            <p class="text-muted small">Masukkan alamat email akun kemitraan Anda untuk melakukan verifikasi data database.</p>
        </div>

        {{-- NOTIFIKASI ERROR JIKA EMAIL TIDAK TERDAFTAR DI MYSQL --}}
        @if($errors->any())
            <div class="alert alert-danger small mb-3 border-0" style="border-radius: 6px;">
                @foreach($errors->all() as $error)
                    <i class="bi bi-exclamation-circle me-1"></i> {{ $error }}
                @endforeach
            </div>
        @endif

        {{-- FORM AKSI MENUNJU LOGIKA CHECKEMAILFORRESET --}}
        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="form-label fw-semibold small text-muted">Email Akun Kemitraan</label>
                <input type="email" name="email" class="form-control" placeholder="contoh@gmail.com" value="{{ old('email') }}" required>
            </div>

            {{-- 🌟 PERUBAHAN: Teks Button Diganti Menjadi Verifikasi Akun --}}
            <button type="submit" class="btn w-100 btn-reset fw-semibold mb-3">Verifikasi Email</button>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-decoration-none small fw-semibold text-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Login
                </a>
            </div>
        </form>

    </div>
</div>

</body>
</html>