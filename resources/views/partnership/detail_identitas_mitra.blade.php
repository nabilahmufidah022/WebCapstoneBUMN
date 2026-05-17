@extends('layout.index')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('list_mitra') }}" class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm">
            <i class="bx bx-left-arrow-alt"></i> Kembali ke Pusat Data
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        {{-- ==========================================================================
             CARD HEADER: DILENGKAPI TOMBOL INTERVENSI BYPASS ADMIN
             ========================================================================== --}}
        <div class="card-header bg-primary py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-white mb-0">Detail Profil Lengkap Mitra</h5>

            {{-- Tombol Edit Khusus Hak Akses Admin Untuk Skenario Penanganan PIC Hilang Kabur --}}
            <a href="/profile?user_id={{ $mitra->user_id }}" class="btn btn-sm btn-light text-primary rounded-pill fw-bold px-3 shadow-sm">
                <i class="bx bx-edit-alt me-1"></i> Edit Profil Mitra
            </a>
        </div>

        <div class="card-body p-4">
            <div class="row g-4">
                {{-- Bagian Identitas Perusahaan --}}
                <div class="col-md-6">
                    <label class="small fw-bold text-muted text-uppercase d-block mb-1">Nama Perusahaan</label>
                    <p class="h5 fw-bold text-primary">{{ $mitra->nama_perusahaan }}</p>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold text-muted text-uppercase d-block mb-1">Bidang Usaha</label>
                    <p class="h5 fw-semibold">{{ $mitra->bidang_perusahaan }}</p>
                </div>

                <div class="col-12"><hr class="my-2 text-muted opacity-25"></div>

                {{-- Bagian Informasi PIC --}}
                <div class="col-md-6">
                    <label class="small fw-bold text-muted text-uppercase d-block mb-1">Nama PIC / Pemilik</label>
                    <p class="fs-6 fw-semibold text-dark">{{ $mitra->nama_lengkap }}</p>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold text-muted text-uppercase d-block mb-1">Nomor Telepon (WA)</label>
                    <p class="fs-6 fw-bold text-success">
                        <i class="bx bxl-whatsapp"></i> {{ $mitra->no_telepon }}
                    </p>
                </div>

                {{-- Bagian Lokasi --}}
                <div class="col-12">
                    <label class="small fw-bold text-muted text-uppercase d-block mb-1">Lokasi / Alamat Perusahaan</label>
                    <p class="fs-6">{{ $mitra->lokasi_perusahaan }}</p>
                </div>

                {{-- Bagian Deskripsi --}}
                <div class="col-12">
                    <label class="small fw-bold text-muted text-uppercase d-block mb-1">Deskripsi Usaha</label>
                    <div class="p-3 bg-light rounded-3 border">
                        <p class="fs-6 mb-0 text-secondary" style="white-space: pre-line;">{{ $mitra->deskripsi_perusahaan ?? 'Tidak ada deskripsi yang dicantumkan.' }}</p>
                    </div>
                </div>

                <div class="col-12"><hr class="my-2 text-muted opacity-25"></div>

                {{-- Bagian Dokumen Pendukung --}}
                <div class="col-md-6">
                    <label class="small fw-bold text-muted text-uppercase d-block mb-2">Dokumen Company Profile</label>
                    @if($mitra->company_profile)
                        <div class="d-flex align-items-center p-3 border rounded-3 bg-white shadow-sm">
                            <i class="bx bxs-file-pdf fs-1 text-danger me-3"></i>
                            <div class="flex-grow-1 overflow-hidden">
                                <small class="d-block text-muted fw-bold">FILE PDF</small>
                                <small class="d-block text-truncate text-secondary">{{ $mitra->company_profile }}</small>
                            </div>
                            <a href="{{ asset('uploads/mitra/' . $mitra->company_profile) }}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3">
                                <i class="bx bx-show me-1"></i> Lihat
                            </a>
                        </div>
                    @else
                        <div class="p-3 border rounded-3 bg-light text-center">
                            <small class="text-muted">Tidak ada dokumen Company Profile</small>
                        </div>
                    @endif
                </div>

                <div class="col-md-6">
                    <label class="small fw-bold text-muted text-uppercase d-block mb-2">Surat Permohonan Audiensi</label>
                    @if($mitra->surat_permohonan_audiensi)
                        <div class="d-flex align-items-center p-3 border rounded-3 bg-white shadow-sm">
                            <i class="bx bxs-file-pdf fs-1 text-danger me-3"></i>
                            <div class="flex-grow-1 overflow-hidden">
                                <small class="d-block text-muted fw-bold">FILE PDF</small>
                                <small class="d-block text-truncate text-secondary">{{ $mitra->surat_permohonan_audiensi }}</small>
                            </div>
                            <a href="{{ asset('uploads/mitra/' . $mitra->surat_permohonan_audiensi) }}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3">
                                <i class="bx bx-show me-1"></i> Lihat
                            </a>
                        </div>
                    @else
                        <div class="p-3 border rounded-3 bg-light text-center">
                            <small class="text-muted">Tidak ada Surat Permohonan Audiensi</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
