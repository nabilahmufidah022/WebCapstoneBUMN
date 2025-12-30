@extends('layout.index')

@section('content')
<div class="container">
    <h1>Daftar Mitra Baru</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('store_mitra') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="no_telepon">No. Telepon</label>
            <input type="text" id="no_telepon" name="no_telepon" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="nama_perusahaan">Nama Perusahaan</label>
            <input type="text" id="nama_perusahaan" name="nama_perusahaan" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="bidang_perusahaan">Bidang Perusahaan</label>
            <input type="text" id="bidang_perusahaan" name="bidang_perusahaan" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="lokasi_perusahaan">Lokasi Perusahaan</label>
            <input type="text" id="lokasi_perusahaan" name="lokasi_perusahaan" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="deskripsi_perusahaan">Deskripsi Singkat Perusahaan</label>
            <textarea id="deskripsi_perusahaan" name="deskripsi_perusahaan" class="form-control" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="company_profile">Dokumen Company Profile</label>
            <input type="file" id="company_profile" name="company_profile" class="form-control" accept=".pdf,.doc,.docx">
            <small class="form-text text-muted">Format: PDF, DOC, DOCX. Maksimal 5MB.</small>
        </div>

        <div class="form-group">
            <label for="surat_permohonan_audiensi">Surat Permohonan Audiensi</label>
            <input type="file" id="surat_permohonan_audiensi" name="surat_permohonan_audiensi" class="form-control" accept=".pdf,.doc,.docx">
            <small class="form-text text-muted">Format: PDF, DOC, DOCX. Maksimal 5MB.</small>
        </div>

        <button type="submit" class="btn btn-primary" @if($hasMitra) disabled @endif>Daftar Mitra</button>
    </form>

    @if($hasMitra)
        <div class="alert alert-info mt-3">
            Anda sudah mendaftar sebagai mitra. Tidak dapat mendaftar lagi.
        </div>
    @endif
</div>
@endsection
