@extends('layout.index')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Daftar Mitra Baru</h1>

    {{-- KOMPONEN TANGKAPAN ERROR VALIDASI GLOBAL (Alert Box Atas) --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-4" style="border-radius: 12px; border: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <h6 class="fw-bold small mb-2"><i class="bx bx-error-circle me-1"></i> Pendaftaran Gagal! Silakan periksa kembali inputan Anda:</h6>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('store_mitra') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- INPUT NAMA LENGKAP --}}
        <div class="form-group mb-4">
            <label for="nama_lengkap" class="form-label fw-semibold text-muted small mb-1">Nama Lengkap</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap') }}" required>
            @error('nama_lengkap')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- INPUT NO TELEPON --}}
        <div class="form-group mb-4">
            <label for="no_telepon" class="form-label fw-semibold text-muted small mb-1">No. Telepon</label>
            <input type="text" id="no_telepon" name="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror" value="{{ old('no_telepon') }}" required>
            @error('no_telepon')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- INPUT NAMA PERUSAHAAN --}}
        <div class="form-group mb-4">
            <label for="nama_perusahaan" class="form-label fw-semibold text-muted small mb-1">Nama Perusahaan</label>
            <input type="text" id="nama_perusahaan" name="nama_perusahaan" class="form-control @error('nama_perusahaan') is-invalid @enderror" value="{{ old('nama_perusahaan') }}" required>
            @error('nama_perusahaan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- INPUT MULTI-CHECKBOX BIDANG USAHA --}}
        <div class="form-group mb-4">
            <label class="form-label fw-semibold text-muted small mb-2">Bidang Usaha / Kategori Pelatihan (Bisa pilih lebih dari satu)</label>
            <div class="p-3 bg-white rounded border @error('bidang_perusahaan') border-danger @enderror">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="bidang_perusahaan[]" value="Literasi Digital" id="digital"
                        {{ is_array(old('bidang_perusahaan')) && in_array('Literasi Digital', old('bidang_perusahaan')) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold text-dark small" for="digital">
                        Literasi Digital
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="bidang_perusahaan[]" value="Literasi Bisnis" id="bisnis"
                        {{ is_array(old('bidang_perusahaan')) && in_array('Literasi Bisnis', old('bidang_perusahaan')) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold text-dark small" for="bisnis">
                        Literasi Bisnis
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="bidang_perusahaan[]" value="Literasi Dasar" id="dasar"
                        {{ is_array(old('bidang_perusahaan')) && in_array('Literasi Dasar', old('bidang_perusahaan')) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold text-dark small" for="dasar">
                        Literasi Dasar
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="bidang_perusahaan[]" value="Tematik" id="tematik"
                        {{ is_array(old('bidang_perusahaan')) && in_array('Tematik', old('bidang_perusahaan')) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold text-dark small" for="tematik">
                        Tematik
                    </label>
                </div>
            </div>
            @error('bidang_perusahaan')
                <div class="text-danger small mt-1" style="font-size: 80%;">{{ $message }}</div>
            @enderror
        </div>

        {{-- INPUT LOKASI PERUSAHAAN --}}
        <div class="form-group mb-4">
            <label for="lokasi_perusahaan" class="form-label fw-semibold text-muted small mb-1">Lokasi Perusahaan</label>
            <input type="text" id="lokasi_perusahaan" name="lokasi_perusahaan" class="form-control @error('lokasi_perusahaan') is-invalid @enderror" value="{{ old('lokasi_perusahaan') }}" required>
            @error('lokasi_perusahaan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- INPUT DESKRIPSI PERUSAHAAN --}}
        <div class="form-group mb-4">
            <label for="deskripsi_perusahaan" class="form-label fw-semibold text-muted small mb-1">Deskripsi Singkat Perusahaan</label>
            <textarea id="deskripsi_perusahaan" name="deskripsi_perusahaan" class="form-control @error('deskripsi_perusahaan') is-invalid @enderror" rows="4" required>{{ old('deskripsi_perusahaan') }}</textarea>
            @error('deskripsi_perusahaan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- INPUT FILE COMPANY PROFILE --}}
        <div class="form-group mb-4">
            <label for="company_profile" class="form-label fw-semibold text-muted small mb-1">Dokumen Company Profile</label>
            <input type="file" id="company_profile" name="company_profile" class="form-control @error('company_profile') is-invalid @enderror" accept=".pdf,.doc,.docx">
            <small class="form-text text-muted mt-1 d-block" style="font-size: 11px;">Format: PDF, DOC, DOCX. Maksimal 5MB.</small>
            @error('company_profile')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- INPUT FILE SURAT PERMOHONAN AUDIENSI --}}
        <div class="form-group mb-4">
            <label for="surat_permohonan_audiensi" class="form-label fw-semibold text-muted small mb-1">Surat Permohonan Audiensi</label>
            <input type="file" id="surat_permohonan_audiensi" name="surat_permohonan_audiensi" class="form-control @error('surat_permohonan_audiensi') is-invalid @enderror" accept=".pdf,.doc,.docx">
            <small class="form-text text-muted mt-1 d-block" style="font-size: 11px;">Format: PDF, DOC, DOCX. Maksimal 5MB.</small>
            @error('surat_permohonan_audiensi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold" @if($hasMitra) disabled @endif>Daftar Mitra</button>
        </div>
    </form>

    @if($hasMitra)
        <div class="alert alert-info mt-4 border-0 shadow-sm">
            <i class="bx bx-info-circle me-1"></i> Anda sudah mendaftar sebagai mitra. Tidak dapat mendaftar lagi.
        </div>
    @endif
</div>
@endsection
