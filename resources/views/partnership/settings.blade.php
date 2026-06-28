@extends('layout.index')

@section('content')

<div class="container-xl px-4 mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Change Password</div>
                <div class="card-body">

                    {{-- KOMPONEN NOTIFIKASI SUKSES --}}
                    @if (session('success'))
                        <div class="alert alert-success border-0 rounded-3 mb-4 small" style="background-color: #e6f4ea; color: #137333;">
                            <i class="bx bxs-check-circle me-1"></i> {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('changePassword') }}">
                        @csrf
                        <div class="row">
                            
                            {{-- 1. CURRENT PASSWORD --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="small mb-1" for="current_password">Current Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('current_password', 'toggleOldIcon')">
                                            <i class="bx bx-hide" id="toggleOldIcon"></i>
                                        </button>
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- 2. NEW PASSWORD --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="small mb-1" for="password">New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required minlength="8">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password', 'toggleNewIcon')">
                                            <i class="bx bx-hide" id="toggleNewIcon"></i>
                                        </button>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- 3. CONFIRM PASSWORD --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="small mb-1" for="password_confirmation">Confirm Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password_confirmation', 'toggleConfirmIcon')">
                                            <i class="bx bx-hide" id="toggleConfirmIcon"></i>
                                        </button>
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                        </div>
                        <button class="btn btn-primary" type="submit">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 🌟 JAVASCRIPT TOGGLE PASSWORD VISIBILITY --}}
<script>
    function togglePasswordVisibility(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(iconId);

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleIcon.classList.remove("bx-hide");
            toggleIcon.classList.add("bx-show");
        } else {
            passwordInput.type = "password";
            toggleIcon.classList.remove("bx-show");
            toggleIcon.classList.add("bx-hide");
        }
    }
</script>

@endsection