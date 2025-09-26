@extends('layout.index')

@section('content')

@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="container-xl px-4 mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Change Password</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('changePassword') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="small mb-1" for="current_password">Current Password</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="small mb-1" for="password">New Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required minlength="8">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="small mb-1" for="password_confirmation">Confirm Password</label>
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required>
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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

@endsection
