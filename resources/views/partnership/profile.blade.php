@extends('layout.index')

@section('content')

@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="container-xl px-4 mt-4">
    <form method="POST" action="{{ route('updateProfile') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            {{-- Profile Picture Card --}}
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

            {{-- Account Details Card --}}
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

                        {{-- Section Data Bisnis Kemitraan (Bisa Diedit Dinamis oleh Mitra) --}}
                        @if($mitra)
                            <div class="mb-3">
                                <label class="small mb-1" for="nama_perusahaan">Nama Perusahaan <span class="text-muted">(🔒 Dikunci Sistem)</span></label>
                                {{-- Tetap readonly agar data entitas nama tidak dimanipulasi --}}
                                <input type="text" class="form-control text-primary fw-bold" id="nama_perusahaan" name="nama_perusahaan" value="{{ old('nama_perusahaan', $mitra->nama_perusahaan) }}" readonly style="background-color: #f8fafc;">
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="bidang_perusahaan">Bidang Usaha</label>
                                {{-- 🔓 Buka Gembok: Atribut readonly dibuang --}}
                                <input type="text" class="form-control" id="bidang_perusahaan" name="bidang_perusahaan" value="{{ old('bidang_perusahaan', is_array($mitra->bidang_perusahaan) ? implode(', ', $mitra->bidang_perusahaan) : $mitra->bidang_perusahaan) }}">
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="nama_lengkap">Nama PIC / Pemilik</label>
                                {{-- 🔓 Buka Gembok: Atribut readonly dibuang --}}
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $mitra->nama_lengkap) }}">
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="no_telepon">Nomor Telepon (WA)</label>
                                {{-- 🔓 Buka Gembok: Atribut readonly dibuang --}}
                                <input type="text" class="form-control" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $mitra->no_telepon) }}">
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="deskripsi_perusahaan">Deskripsi Usaha</label>
                                {{-- 🔓 Buka Gembok: Atribut readonly dibuang --}}
                                <textarea class="form-control" id="deskripsi_perusahaan" name="deskripsi_perusahaan" rows="3" style="resize: none;">{{ $mitra->deskripsi_perusahaan }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="small mb-1" for="lokasi_perusahaan">Lokasi / Alamat Perusahaan</label>
                                {{-- 🔓 Buka Gembok: Atribut readonly dibuang --}}
                                <textarea class="form-control" id="lokasi_perusahaan" name="lokasi_perusahaan" rows="3" style="resize: none;">{{ $mitra->lokasi_perusahaan }}</textarea>
                            </div>

                        @elseif($user->usertype === 'user' || $user->usertype === 'mitra')
                            <div class="alert alert-warning my-3" style="border-radius: 8px;">
                                <span class="small fw-semibold"><i class="bx bx-info-circle me-1"></i> Data profil bisnis kemitraan Anda belum tersedia. Silakan selesaikan alur pendaftaran administrasi mitra terlebih dahulu agar tervalidasi ke dalam sistem Pusat Data.</span>
                            </div>
                        @endif

                        {{-- Tombol Utama: Simpan Perubahan Profil & Handover PIC --}}
                        <div class="mt-4 d-flex justify-content-between align-items-center">
                            <button class="btn btn-success px-4" type="submit">Simpan Perubahan</button>

                            {{-- Button Trigger Modal Handover PIC --}}
                            @if($user->usertype !== 'admin' && $user->mitra)
                                <button class="btn btn-primary px-4" type="button" data-bs-toggle="modal" data-bs-target="#handoverPicModal">
                                    <i class="bx bx-transfer-alt me-1"></i> Handover PIC Baru
                                </button>
                            @endif
                        </div>

                        {{-- Danger Zone Section --}}
                        <hr class="my-4">
                        <div class="p-3 bg-light rounded" style="border: 1px solid #fecaca;">
                            <h6 class="text-danger fw-bold small mb-1">Danger Zone</h6>
                            <p class="text-muted small mb-2">Tindakan ini akan menghapus akun Anda secara permanen dari sistem Rumah BUMN.</p>
                            <button class="btn btn-outline-danger btn-sm" type="button" onclick="confirmDeleteAccount()">Hapus Akun</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- ==========================================================================
         MODAL COMPONENT: FORM HANDOVER PIC BARU
         ========================================================================== --}}
    @if($user->usertype !== 'admin' && $user->mitra)
    <div class="modal fade" id="handoverPicModal" tabindex="-1" aria-labelledby="handoverPicModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary" id="handoverPicModalLabel">Form Handover PIC Kemitraan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('profile.handover') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info small mb-3" style="border-radius: 6px;">
                            <i class="bx bx-info-circle me-1"></i> <strong>Informasi:</strong> Mengisi form ini akan mengalihkan akses nama akun pengguna dan sinkronisasi penanggung jawab entitas mitra langsung kepada PIC pengganti.
                        </div>

                        <div class="mb-3">
                            <label class="small mb-1 fw-semibold" for="new_pic_name">Nama PIC Baru</label>
                            <input type="text" class="form-control" id="new_pic_name" name="new_pic_name" placeholder="Masukkan nama lengkap PIC baru" required>
                        </div>

                        <div class="mb-3">
                            <label class="small mb-1 fw-semibold" for="new_pic_email">Email PIC Baru</label>
                            <input type="email" class="form-control" id="new_pic_email" name="new_pic_email" placeholder="Masukkan email aktif PIC baru" required>
                        </div>

                        <div class="mb-3">
                            <label class="small mb-1 fw-semibold" for="new_pic_phone">Nomor Telepon (WA) PIC Baru</label>
                            <input type="text" class="form-control" id="new_pic_phone" name="new_pic_phone" placeholder="Contoh: 0812XXXXXXXX" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin data PIC baru sudah benar dan ingin memproses alih tanggung jawab?')">Proses Handover</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Form hidden untuk delete account --}}
    <form id="delete-account-form" action="{{ route('profile.destroy') }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmDeleteAccount() {
            if (confirm('Apakah Anda yakin ingin menghapus akun ini secara permanen?')) {
                document.getElementById('delete-account-form').submit();
            }
        }
    </script>
</div>
@endsection
