<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | RUMAH BUMN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f9f9f9; }
        .reset-card { background: #fff; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 40px; max-width: 450px; width: 100%; }
        .form-control { background: #f2f2f2; border: none; border-radius: 6px; height: 45px; }
        .input-group-text-custom { background: #f2f2f2; border: none; border-radius: 0 6px 6px 0; color: #6c757d; cursor: pointer; }
        .input-control-custom { border-radius: 6px 0 0 6px !important; }
        .btn-update { background: #2f318b; color: #fff; height: 50px; font-size: 16px; border-radius: 6px; }
        .btn-update:hover { background: #23245f; color: #fff; }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="reset-card">
        <div class="text-center mb-4">
            <h5 class="fw-bold text-dark">Buat Password Baru</h5>
            <p class="text-muted small">Silakan ketik kombinasi kata sandi baru (min. 8 karakter) untuk akun Anda.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger small mb-3 border-0" style="border-radius: 6px; background-color: #f8d7da; color: #842029;">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.update.execute') }}" method="POST">
            @csrf
            {{-- Mengunci email target dari session flash server maupun old input --}}
            <input type="hidden" name="email" value="{{ session('reset_email') ?? old('email') }}">

            <div class="mb-3">
                <label class="form-label fw-semibold small text-muted">Password Baru</label>
                <div class="input-group">
                    {{-- 🌟 SINKRONISASI FRONTEND: Menahan isi ketikan lama menggunakan old('password') --}}
                    <input type="password" id="password" name="password" class="form-control input-control-custom" placeholder="Minimal 8 karakter" value="{{ old('password') }}" required>
                    <span class="input-group-text input-group-text-custom px-3 toggle-password" data-target="password">
                        <i class="bi bi-eye"></i>
                    </span>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small text-muted">Konfirmasi Password Baru</label>
                <div class="input-group">
                    {{-- 🌟 SINKRONISASI FRONTEND: Menahan isi ketikan lama menggunakan old('password_confirmation') --}}
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control input-control-custom" placeholder="Ulangi password" value="{{ old('password_confirmation') }}" required>
                    <span class="input-group-text input-group-text-custom px-3 toggle-password" data-target="password_confirmation">
                        <i class="bi bi-eye"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn w-100 btn-update fw-semibold mb-3">Simpan Password Baru</button>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    });
</script>
</body>
</html>