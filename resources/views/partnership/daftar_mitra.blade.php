@extends('layout.index')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Daftar Mitra Baru</h1>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('store_mitra') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group mb-4">
            <label for="nama_lengkap" class="form-label fw-semibold text-muted small mb-1">Nama Lengkap</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" required>
        </div>

        <div class="form-group mb-4">
            <label for="no_telepon" class="form-label fw-semibold text-muted small mb-1">No. Telepon</label>
            <input type="text" id="no_telepon" name="no_telepon" class="form-control" required>
        </div>

        <div class="form-group mb-4">
            <label for="nama_perusahaan" class="form-label fw-semibold text-muted small mb-1">Nama Perusahaan</label>
            <input type="text" id="nama_perusahaan" name="nama_perusahaan" class="form-control" required>
        </div>

        <div class="form-group mb-4">
            <label class="form-label fw-semibold text-muted small mb-2">Bidang Usaha / Kategori Pelatihan (Bisa pilih lebih dari satu)</label>
            <div class="p-3 bg-white rounded border">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="bidang_perusahaan[]" value="Literasi Digital" id="digital">
                    <label class="form-check-label fw-semibold text-dark small" for="digital">
                        Literasi Digital
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="bidang_perusahaan[]" value="Literasi Bisnis" id="bisnis">
                    <label class="form-check-label fw-semibold text-dark small" for="bisnis">
                        Literasi Bisnis
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="bidang_perusahaan[]" value="Literasi Dasar" id="dasar">
                    <label class="form-check-label fw-semibold text-dark small" for="dasar">
                        Literasi Dasar
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="bidang_perusahaan[]" value="Tematik" id="tematik">
                    <label class="form-check-label fw-semibold text-dark small" for="tematik">
                        Tematik
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group mb-4">
            <label for="lokasi_perusahaan" class="form-label fw-semibold text-muted small mb-1">Lokasi Perusahaan</label>
            <input type="text" id="lokasi_perusahaan" name="lokasi_perusahaan" class="form-control" required>
        </div>

        <div class="form-group mb-4">
            <label for="deskripsi_perusahaan" class="form-label fw-semibold text-muted small mb-1">Deskripsi Singkat Perusahaan</label>
            <textarea id="deskripsi_perusahaan" name="deskripsi_perusahaan" class="form-control" rows="4" required></textarea>
        </div>

        <div class="form-group mb-4">
            <label for="company_profile" class="form-label fw-semibold text-muted small mb-1">Dokumen Company Profile</label>
            <input type="file" id="company_profile" name="company_profile" class="form-control" accept=".pdf,.doc,.docx">
            <small class="form-text text-muted mt-1 d-block" style="font-size: 11px;">Format: PDF, DOC, DOCX. Maksimal 5MB.</small>
        </div>

        <div class="form-group mb-4">
            <label for="surat_permohonan_audiensi" class="form-label fw-semibold text-muted small mb-1">Surat Permohonan Audiensi</label>
            <input type="file" id="surat_permohonan_audiensi" name="surat_permohonan_audiensi" class="form-control" accept=".pdf,.doc,.docx">
            <small class="form-text text-muted mt-1 d-block" style="font-size: 11px;">Format: PDF, DOC, DOCX. Maksimal 5MB.</small>
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
