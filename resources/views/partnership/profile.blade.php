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
                <div class="card mb-4 mb-xl-0" style="min-height: 400px;">
                    <div class="card-header">Profile Picture</div>
                    <div class="card-body text-center">
                        <img class="img-account-profile rounded-circle mb-2" src="{{ $user->profile_image ? asset('img/' . $user->profile_image) : 'http://bootdey.com/img/Content/avatar/avatar1.png' }}" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover;">
                        <div class="small font-italic text-muted mb-4">JPG or PNG no larger than 5 MB</div>
                        <input type="file" class="form-control @error('profile_image') is-invalid @enderror" id="profile_image" name="profile_image" accept="image/*">
                        @error('profile_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <div class="card mb-4" style="min-height: 400px;">
                    <div class="card-header">Account Details</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small mb-1" for="name">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1" for="email">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ==========================================================================
                             FITUR INTEGRASI REALTIME: MENYATU LANGSUNG DI ACCOUNT DETAILS
                             Akses Edit Fleksibel: Admin bisa edit, Mitra hanya bisa melihat (Readonly)
                             ========================================================================== --}}
                        @if($mitra)

                            <div class="mb-3">
                                <label class="small mb-1" for="nama_perusahaan">Nama Perusahaan</label>
                                <input type="text" class="form-control text-primary fw-bold" id="nama_perusahaan" name="nama_perusahaan" value="{{ old('nama_perusahaan', $mitra->nama_perusahaan) }}" {{ $user->usertype === 'admin' ? '' : 'readonly' }} style="{{ $user->usertype === 'admin' ? '' : 'background-color: #f8fafc;' }}">
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="bidang_perusahaan">Bidang Usaha</label>
                                <input type="text" class="form-control" id="bidang_perusahaan" name="bidang_perusahaan" value="{{ old('bidang_perusahaan', $mitra->bidang_perusahaan) }}" {{ $user->usertype === 'admin' ? '' : 'readonly' }} style="{{ $user->usertype === 'admin' ? '' : 'background-color: #f8fafc;' }}">
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="nama_lengkap">Nama PIC / Pemilik</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $mitra->nama_lengkap) }}" {{ $user->usertype === 'admin' ? '' : 'readonly' }} style="{{ $user->usertype === 'admin' ? '' : 'background-color: #f8fafc;' }}">
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="no_telepon">Nomor Telepon (WA)</label>
                                <input type="text" class="form-control" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $mitra->no_telepon) }}" {{ $user->usertype === 'admin' ? '' : 'readonly' }} style="{{ $user->usertype === 'admin' ? '' : 'background-color: #f8fafc;' }}">
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="deskripsi_perusahaan">Deskripsi Usaha</label>
                                <textarea class="form-control" id="deskripsi_perusahaan" name="deskripsi_perusahaan" rows="3" {{ $user->usertype === 'admin' ? '' : 'readonly' }} style="resize: none; {{ $user->usertype === 'admin' ? '' : 'background-color: #f8fafc;' }}">{{ $mitra->deskripsi_perusahaan }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="small mb-1" for="lokasi_perusahaan">Lokasi / Alamat Perusahaan</label>
                                <textarea class="form-control" id="lokasi_perusahaan" name="lokasi_perusahaan" rows="3" {{ $user->usertype === 'admin' ? '' : 'readonly' }} style="resize: none; {{ $user->usertype === 'admin' ? '' : 'background-color: #f8fafc;' }}">{{ $mitra->lokasi_perusahaan }}</textarea>
                            </div>

                        @elseif($user->usertype === 'user' || $user->usertype === 'mitra')
                            <div class="alert alert-warning my-3" style="border-radius: 8px;">
                                <span class="small fw-semibold"><i class="bx bx-info-circle me-1"></i> Data profil bisnis kemitraan Anda belum tersedia. Silakan selesaikan alur pendaftaran administrasi mitra terlebih dahulu agar tervalidasi ke dalam sistem Pusat Data.</span>
                            </div>
                        @endif

                        {{-- Action button baris terbawah komponen halaman profil --}}
                        <div class="d-flex gap-2 mt-2">
                            @if($user->usertype === 'admin')
                                <button class="btn btn-success" type="submit">Update Data Kemitraan (Bypass Admin)</button>
                            @else
                                <button class="btn btn-primary" type="submit">Update Profile</button>
                            @endif
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
