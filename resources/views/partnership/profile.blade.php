@extends('layout.index')

@section('content')

@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="container-xl px-4 mt-4">
    <form method="POST" action="{{ route('updateProfile') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-xl-4">
                <!-- Profile picture card-->
                <div class="card mb-4 mb-xl-0" style="min-height: 400px;">
                    <div class="card-header">Profile Picture</div>
                    <div class="card-body text-center">
                        <!-- Profile picture image-->
                        <img class="img-account-profile rounded-circle mb-2" src="{{ $user->profile_image ? asset('img/' . $user->profile_image) : 'http://bootdey.com/img/Content/avatar/avatar1.png' }}" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover;">
                        <!-- Profile picture help block-->
                        <div class="small font-italic text-muted mb-4">JPG or PNG no larger than 5 MB</div>
                        <!-- Profile picture upload input-->
                        <input type="file" class="form-control @error('profile_image') is-invalid @enderror" id="profile_image" name="profile_image" accept="image/*">
                        @error('profile_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <!-- Account details card-->
                <div class="card mb-4" style="min-height: 400px;">
                    <div class="card-header">Account Details</div>
                    <div class="card-body">
                        <!-- Form Group (name)-->
                        <div class="mb-3">
                            <label class="small mb-1" for="name">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Form Group (email address)-->
                        <div class="mb-3">
                            <label class="small mb-1" for="email">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>  
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" type="submit">Update Profile</button>
                            <button class="btn btn-danger" type="button" onclick="confirmDeleteAccount()">Hapus Akun</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <form id="delete-account-form" action="{{ route('profile.destroy') }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmDeleteAccount() {
            if (confirm('Yakin anda ingin menghapus akun?')) {
                document.getElementById('delete-account-form').submit();
            }
        }
    </script>
</div>

@endsection
