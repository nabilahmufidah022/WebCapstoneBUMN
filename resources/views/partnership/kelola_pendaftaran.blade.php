@extends('layout.index')

@section('content')
<div class="container py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Verifikasi Pendaftaran Kemitraan</h4>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 small" role="alert" style="border-radius: 8px;">
        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- NAVIGASI TAB UTAMA (MANAGEMENT CONTROL) --}}
    <ul class="nav nav-tabs mb-4" id="verifikasiTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold text-dark" id="antrean-tab" data-bs-toggle="tab" data-bs-target="#antrean" type="button" role="tab" aria-controls="antrean" aria-selected="true">
                <i class="bx bx-time-five me-1"></i> Menunggu Verifikasi ({{ $mitras->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-danger" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat" type="button" role="tab" aria-controls="riwayat" aria-selected="false">
                <i class="bx bx-x-circle me-1"></i> Riwayat Pendaftaran Ditolak ({{ $rejectedMitras->count() }})
            </button>
        </li>
    </ul>

    <div class="tab-content" id="verifikasiTabContent">
        
        {{-- ==========================================================================
             TAB 1: DAFTAR ANTREAN PENGAJUAN BARU (STATUS 0 & 1)
             ========================================================================== --}}
        <div class="tab-pane fade show active" id="antrean" role="tabpanel" aria-labelledby="antrean-tab">
            <div class="row">
                @forelse($mitras as $mitra)
                <div class="col-12 mb-3">
                    <div class="card shadow-sm border-0" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <h5 class="fw-bold mb-0 me-2">{{ $mitra->nama_perusahaan }}</h5>
                                        @if($mitra->status == 0)
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @else
                                            <span class="badge bg-info text-white">On Review</span>
                                        @endif
                                    </div>

                                    <div class="text-muted small mb-3">
                                        <i class="bx bx-user"></i> PIC: {{ $mitra->nama_lengkap }} &nbsp;&nbsp;
                                        <i class="bx bx-phone"></i> {{ $mitra->no_telepon }} &nbsp;&nbsp;
                                        <i class="bx bx-map"></i> {{ $mitra->lokasi_perusahaan }}
                                    </div>

                                    <p class="mb-0 text-secondary" style="font-size: 14px;">
                                        {{ Str::limit($mitra->deskripsi_perusahaan, 150) }}
                                    </p>
                                </div>

                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <div class="d-flex justify-content-md-end gap-2">
                                        <a href="{{ route('mitra.detail', $mitra->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                            <i class="bx bx-show"></i> Detail
                                        </a>

                                        @if($mitra->status == 1)
                                            <form action="{{ route('mitra.approve', $mitra->id) }}" method="POST" onsubmit="return confirm('Setujui pendaftaran mitra ini?')">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3">
                                                    <i class="bx bx-check"></i> Setujui
                                                </button>
                                            </form>

                                            <form action="{{ route('mitra.reject', $mitra->id) }}" method="POST" onsubmit="return confirm('Tolak pendaftaran mitra ini?')">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3">
                                                    <i class="bx bx-x"></i> Tolak
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-success btn-sm opacity-50 rounded-pill px-3" disabled style="cursor: not-allowed;" title="Harap tinjau berkas di halaman detail terlebih dahulu">
                                                <i class="bx bx-check"></i> Setujui
                                            </button>

                                            <button type="button" class="btn btn-danger btn-sm opacity-50 rounded-pill px-3" disabled style="cursor: not-allowed;" title="Harap tinjau berkas di halaman detail terlebih dahulu">
                                                <i class="bx bx-x"></i> Tolak
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-white shadow-sm" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                        <i class="bx bx-envelope-open fs-1 text-muted"></i>
                        <p class="mt-3 text-muted">Tidak ada pengajuan pendaftaran mitra baru saat ini.</p>
                        <a href="{{ route('list_mitra') }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">Lihat Pusat Data Mitra</a>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ==========================================================================
             TAB 2: RIWAYAT REKAM DATA MITRA YANG DITOLAK (STATUS 3)
             ========================================================================== --}}
        <div class="tab-pane fade" id="riwayat" role="tabpanel" aria-labelledby="refresh-tab">
            <div class="row">
                @forelse($rejectedMitras as $rm)
                <div class="col-12 mb-3">
                    <div class="card shadow-sm border-0" style="border-radius: 12px; background-color: #fff5f5; border-left: 5px solid #dc3545 !important;">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-9">
                                    <div class="d-flex align-items-center mb-2">
                                        <h5 class="fw-bold text-dark mb-0 me-2">{{ $rm->nama_perusahaan }}</h5>
                                        <span class="badge bg-danger text-white">Ditolak Admin</span>
                                    </div>

                                    <div class="text-muted small mb-3">
                                        <i class="bx bx-user"></i> PIC: {{ $rm->nama_lengkap }} &nbsp;&nbsp;
                                        <i class="bx bx-phone"></i> {{ $rm->no_telepon }} &nbsp;&nbsp;
                                        <i class="bx bx-calendar-x"></i> Waktu Penolakan: {{ $rm->updated_at->format('d-m-Y H:i') }} WIB
                                    </div>

                                    <div class="p-3 rounded" style="background-color: #fff; border: 1px dashed #f5c2c7;">
                                        <strong class="small text-danger d-block mb-1"><i class="bx bx-info-circle"></i> Catatan Riwayat Sistem:</strong>
                                        <p class="mb-0 text-secondary small style="font-style: italic;">
                                            {{-- 🌟 SECURE NULL-SAFETY: Proteksi berlapis agar anti-eror jika nama relasi model berbeda --}}
                                            {{ $rm->historyMitras?->where('action', 'rejected')->first()?->description ?? ($rm->history?->where('action', 'rejected')->first()?->description ?? 'Pendaftaran ditolak oleh manajemen admin Rumah BUMN Jakarta.') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                    <a href="{{ route('mitra.detail', $rm->id) }}" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                        <i class="bx bx-file"></i> Audit Histori
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-white shadow-sm" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                        <i class="bx bx-folder-open fs-1 text-muted"></i>
                        <p class="mt-3 text-muted mb-0">Belum ada rekaman riwayat pendaftaran mitra yang ditolak di sistem database.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection