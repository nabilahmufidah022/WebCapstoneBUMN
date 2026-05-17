@extends('layout.index')

@section('content')
<div class="container-fluid px-4 mt-4 participation-detail">

    <div class="mb-4">
        <h5 class="fw-bold mb-1 text-dark">Detail Pelatihan & Keikutsertaan</h5>
        <p class="text-muted mb-0" style="font-size: 13px;">
            Informasi lengkap mengenai jadwal, lokasi, dan evaluasi kegiatan mitra.
        </p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="p-4">
                <div class="detail-row">
                    <span class="detail-label">Nama Perusahaan Mitra</span>
                    <span class="detail-value text-primary">{{ $participation->mitra->nama_perusahaan }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Judul Pelatihan</span>
                    <span class="detail-value">{{ $participation->judul_pelatihan }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Kategori / Jenis</span>
                    <span class="detail-value"><span class="badge bg-light text-dark border">{{ $participation->kategori }}</span></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Waktu Pelaksanaan</span>
                    <span class="detail-value">
                        <div class="fw-bold">{{ \Carbon\Carbon::parse($participation->tanggal_pelatihan)->translatedFormat('d F Y') }}</div>
                        <div class="small text-muted">{{ $participation->waktu_pelatihan }} WIB</div>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Tempat & Narasumber</span>
                    <span class="detail-value">
                        <div>{{ $participation->tempat_pelatihan }}</div>
                        <div class="small text-muted">Pemateri: {{ $participation->narasumber }}</div>
                    </span>
                </div>

                <div class="detail-row border-0">
                    <span class="detail-label">Status & Pelaksanaan</span>
                    <span class="detail-value">
                        <span class="badge {{ $participation->pelaksanaan == 'online' ? 'badge-online' : 'badge-offline' }}">
                            {{ ucfirst($participation->pelaksanaan) }}
                        </span>
                        <span class="ms-2 fw-bold {{ $participation->status == 'Selesai' ? 'text-success' : 'text-warning' }}">
                            <i class="bx {{ $participation->status == 'Selesai' ? 'bx-check-double' : 'bx-time' }}"></i>
                            {{ $participation->status }}
                        </span>
                    </span>
                </div>

                {{-- ==========================================================
                     AREA VISUALISASI EVALUASI (TAMBAHAN)
                     ========================================================== --}}
                @if($participation->status == 'Selesai')
                <div class="row g-3 mt-3">
                    {{-- 1. Monitoring Internal Admin (HANYA UNTUK ADMIN) --}}
                    @if(auth()->user()->usertype === 'admin')
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light h-100">
                            <h6 class="fw-bold small mb-2 text-dark"><i class="bx bx-star me-1 text-warning"></i> Monitoring Internal Admin</h6>
                            @if(!is_null($participation->rating))
                                <div class="text-warning mb-1">
                                    @for($i=1; $i<=5; $i++)
                                        <i class="bx {{ $i <= $participation->rating ? 'bxs-star' : 'bx-star' }}"></i>
                                    @endfor
                                    <span class="text-dark fw-bold ms-1" style="font-size: 12px;">({{ $participation->rating }}/5)</span>
                                </div>
                                <p class="text-muted mb-0" style="font-size: 12px; font-style: italic;">"{{ $participation->catatan_internal ?? 'Tidak ada catatan.' }}"</p>
                            @else
                                <p class="text-muted small mb-0">Admin belum memberikan penilaian kinerja.</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- 2. Feedback dari Mitra (Bisa dilihat Admin & Mitra) --}}
                    <div class="{{ auth()->user()->usertype === 'admin' ? 'col-md-6' : 'col-md-12' }}">
                        <div class="p-3 rounded-3 border h-100" style="border-style: dashed !important;">
                            <h6 class="fw-bold small mb-2 text-dark">
                                <i class="bx bx-message-square-detail me-1 text-success"></i>
                                {{ auth()->user()->usertype === 'admin' ? 'Feedback dari Mitra' : 'Feedback Anda (Mitra)' }}
                            </h6>
                            @if(!is_null($participation->rating_mitra))
                                <div class="badge bg-success-soft text-success mb-1" style="font-size: 10px;">Skor Kepuasan: {{ $participation->rating_mitra }}/5</div>
                                <p class="text-muted mb-0" style="font-size: 12px; font-style: italic;">"{{ $participation->feedback_mitra }}"</p>
                            @else
                                <p class="text-muted small mb-0">Feedback belum dikirimkan.</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="bg-light p-4 d-flex justify-content-between align-items-center">

                <div class="d-flex gap-2">
                    {{-- LOGIKA TOMBOL KEMBALI: Mitra kembali ke Dashboard, Admin kembali ke Index Silabus --}}
                    @if(auth()->user()->usertype === 'mitra')
                        <a href="{{ route('dashboard') }}" class="btn btn-white border rounded-pill px-4 btn-sm fw-bold shadow-sm">
                            <i class="bx bx-left-arrow-alt me-1"></i> Kembali ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('mitra.participation.index') }}" class="btn btn-white border rounded-pill px-4 btn-sm fw-bold shadow-sm">
                            <i class="bx bx-left-arrow-alt me-1"></i> Kembali ke Silabus
                        </a>
                    @endif

                    @if($participation->status == 'Selesai')

                        @if(auth()->user()->usertype === 'admin' && is_null($participation->rating))
                        <button class="btn btn-warning btn-sm rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#rateModal">
                            <i class="bx bx-star me-1"></i> Beri Penilaian Mitra
                        </button>
                        @endif

                        @if(auth()->user()->usertype === 'mitra' && is_null($participation->rating_mitra))
                        <button class="btn btn-success btn-sm rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                            <i class="bx bx-chat me-1"></i> Kirim Evaluasi Kegiatan
                        </button>
                        @endif

                        {{-- Tombol Lihat Evaluasi HANYA muncul untuk Mitra sebagai arsip --}}
                        @if(auth()->user()->usertype === 'mitra' && !is_null($participation->rating_mitra))
                        <a href="{{ route('mitra.participation.feedback', ['id' => $participation->id, 'section' => 'all']) }}"
                           class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">
                            <i class="bx bx-spreadsheet me-1"></i> Lihat Arsip Feedback
                        </a>
                        @endif

                    @else
                        <div class="d-flex align-items-center bg-white px-3 py-1 rounded-pill border">
                            <i class="bx bx-info-circle text-muted me-2"></i>
                            <span class="text-muted" style="font-size: 11px;">Fitur evaluasi tersedia setelah pelatihan selesai</span>
                        </div>
                    @endif
                </div>

                @if(auth()->user()->usertype === 'admin')
                <form action="{{ route('mitra.participation.destroy', $participation->id) }}" method="POST" onsubmit="return confirm('Hapus data keikutsertaan ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link text-danger btn-sm text-decoration-none p-0 fw-bold">
                        <i class="bx bx-trash me-1"></i> Hapus
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.participation-detail { font-family: 'Inter', sans-serif; }
.detail-row { display: grid; grid-template-columns: 240px 1fr; padding: 16px 0; border-bottom: 1px solid #f8f9fa; font-size: 13px; }
.detail-label { color: #8a94a6; font-weight: 500; }
.detail-value { color: #1e293b; font-weight: 600; }
.badge-online { background-color: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 6px; font-size: 11px; }
.badge-offline { background-color: #dbeafe; color: #1e40af; padding: 4px 12px; border-radius: 6px; font-size: 11px; }
.bg-success-soft { background-color: #e6f4ea; color: #1e7e34; }
.btn-white { background-color: #fff; color: #475569; }
.btn-white:hover { background-color: #f8fafc; }
</style>

{{-- MODAL ADMIN --}}
@if(auth()->user()->usertype === 'admin')
<div class="modal fade" id="rateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('mitra.participation.rate', $participation->id) }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header bg-warning border-0 py-3">
                    <h6 class="modal-title fw-bold text-dark"><i class="bx bx-star me-2"></i>Penilaian Kinerja Mitra</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Berikan rating objektif untuk mitra **{{ $participation->mitra->nama_perusahaan }}** berdasarkan keikutsertaan mereka dalam pelatihan ini.</p>
                    <div class="mb-4 text-center">
                        <label class="form-label-custom d-block mb-3">Rating Kepuasan Admin</label>
                        <div class="star-rating d-flex justify-content-center gap-3">
                            @for($i = 5; $i >= 1; $i--)
                                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" class="btn-check" required>
                                <label for="star{{ $i }}" class="btn btn-outline-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">{{ $i }}</label>
                            @endfor
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label-custom mb-1">Catatan Internal Admin</label>
                        <textarea name="catatan_internal" class="form-control" rows="3" placeholder="Contoh: Mitra sangat kooperatif..." style="font-size: 13px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light btn-sm px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm px-4 rounded-pill fw-bold shadow-sm">Simpan Penilaian</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

@endsection
