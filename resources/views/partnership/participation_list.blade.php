@extends('layout.index')

@section('content')
<style>
    /* 1. SIDEBAR TOTAL CLEANUP - PADET & ELEGAN */
    .main-sidebar {
        width: 200px !important;
    }
    .content-wrapper {
        margin-left: 200px !important;
    }
    .brand-link span {
        color: #000000 !important;
        font-weight: 800 !important;
        font-size: 15px !important;
    }
    .nav-sidebar .nav-link p {
        font-size: 11.5px !important;
        font-weight: 500;
        color: #334155 !important;
    }
    /* HAPUS SEMUA PANAH NYEMPIL */
    .sidebar-arrow-toggle, .bx-chevron-right, .bx-chevron-left, .nav-link i.right, .fa-angle-left, .right {
        display: none !important;
        opacity: 0 !important;
    }

    /* 2. Global Style Halaman */
    .participation-page {
        font-family: 'Inter', sans-serif;
        color: #334155;
        background-color: #f8fafc;
    }

    /* 3. Header Bulan */
    .month-group-header {
        background: #ffffff;
        padding: 12px 20px;
        border-radius: 12px;
        margin-top: 25px;
        margin-bottom: 12px;
        border-left: 6px solid #0d6efd;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .month-group-title {
        font-size: 12px;
        font-weight: 800;
        color: #0d6efd;
        text-transform: uppercase;
    }

    /* 4. Tabel Fit to Screen */
    .table-container-fix {
        width: 100%;
        overflow: hidden;
    }
    .table-custom {
        width: 100%;
        table-layout: fixed;
        margin-bottom: 0;
    }
    .table-custom td {
        padding: 18px 10px;
        vertical-align: top;
        border: none;
    }

    /* 5. Typography */
    .text-label {
        font-size: 9px;
        color: #94a3b8;
        font-weight: 700;
        text-transform: uppercase;
        display: block;
        margin-bottom: 12px;
        min-height: 12px;
    }
    .text-data-main {
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.4;
    }
    .badge-mode {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 9px;
        font-weight: 800;
        background-color: #e0f2fe;
        color: #0369a1;
    }
    .bg-soft-danger {
        background-color: #fee2e2;
        color: #b91c1c;
    }
</style>

<div class="container-fluid py-4 px-4 participation-page">
    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="font-size: 1.2rem; color: #1e293b;">Silabus & Agenda Pelatihan</h5>
            <p class="text-muted mb-0 small">Monitoring jadwal bulanan dan evaluasi kemitraan Rumah BUMN Jakarta.</p>
        </div>

        @if(auth()->user()->usertype === 'admin')
        <button class="btn btn-primary rounded-pill px-4 shadow-sm btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bx bx-plus-circle me-1"></i> Susun Silabus Baru
        </button>
        @endif
    </div>

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body py-3">
            <form class="row g-3 align-items-end" method="GET">
                <div class="col-md-5">
                    <label class="text-label">Cari Agenda Pelatihan</label>
                    <input type="text" name="search" class="form-control form-control-sm border-light bg-light" placeholder="Judul materi atau narasumber..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="text-label">Kategori Literasi</label>
                    <select name="kategori" class="form-select form-select-sm border-light bg-light">
                        <option value="">-- Semua Jenis --</option>
                        <option value="Literasi digital">Literasi Digital</option>
                        <option value="Literasi Bisnis">Literasi Bisnis</option>
                        <option value="Literasi Dasar">Literasi Dasar</option>
                        <option value="Tematik">Tematik</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-secondary btn-sm w-100 fw-bold shadow-sm">Tampilkan Jadwal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Grouping Data --}}
    @php $currentMonth = null; @endphp

    @forelse($participations as $item)
    @php
        $monthYear = \Carbon\Carbon::parse($item->tanggal_pelatihan)->translatedFormat('F Y');
        $tanggalPelatihan = \Carbon\Carbon::parse($item->tanggal_pelatihan);
    @endphp

    @if($monthYear !== $currentMonth)
        <div class="row">
            <div class="col-12">
                <div class="month-group-header">
                    <span class="month-group-title">
                        <i class="bx bx-calendar-event me-2"></i> AGENDA {{ $monthYear }}
                    </span>
                </div>
            </div>
        </div>
        @php $currentMonth = $monthYear; @endphp
    @endif

    <div class="card card-agenda border-0 shadow-sm mb-3" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-container-fix">
                <table class="table table-custom align-middle">
                    <tbody>
                        <tr>
                            <td class="ps-4" style="width: 15%;">
                                <span class="text-label">Tanggal</span>
                                <div class="text-data-main">{{ $tanggalPelatihan->translatedFormat('d M Y') }}</div>
                            </td>

                            <td style="width: 18%;">
                                <span class="text-label">Waktu</span>
                                <div class="d-flex align-items-center gap-1">
                                    <i class="bx bx-time-five text-primary" style="font-size: 14px;"></i>
                                    <span class="text-data-main" style="font-size: 12px;">{{ $item->waktu_pelatihan }}</span>
                                    <span class="small fw-bold text-muted">WIB</span>
                                </div>
                            </td>

                            <td class="text-center" style="width: 10%;">
                                <span class="text-label">Mode</span>
                                <span class="badge-mode {{ $item->pelaksanaan == 'online' ? 'bg-soft-danger' : '' }}">
                                    {{ strtoupper($item->pelaksanaan) }}
                                </span>
                            </td>

                            <td style="width: 25%;">
                                <span class="text-label">Materi Pelatihan</span>
                                <div class="text-data-main text-truncate" title="{{ $item->judul_pelatihan }}">{{ $item->judul_pelatihan }}</div>
                                <div class="text-muted mt-1" style="font-size: 11px;"><i class="bx bx-tag-alt me-1"></i> {{ $item->kategori }}</div>
                            </td>

                            <td style="width: 18%;">
                                <span class="text-label">Mitra Kerjasama</span>
                                <div class="text-data-main text-primary text-truncate">{{ $item->mitra->nama_perusahaan ?? '-' }}</div>
                                <div class="text-muted mt-1 text-truncate" style="font-size: 10px;"><i class="bx bx-map-pin"></i> {{ $item->tempat_pelatihan }}</div>
                            </td>

                            <td class="pe-4 text-center" style="width: 16%;">
                                <span class="text-label">Aksi</span>
                                <div class="d-flex flex-column align-items-center gap-2">

                                    @if($item->status == 'Selesai')
                                        <span class="badge bg-success fw-bold" style="font-size: 9px; border-radius: 4px; padding: 4px 8px;">
                                            <i class="bx bx-check-double"></i> SELESAI
                                        </span>
                                    @elseif($tanggalPelatihan->isToday() || $tanggalPelatihan->isPast())
                                        <span class="badge {{ $tanggalPelatihan->isToday() ? 'bg-primary' : 'bg-danger' }} fw-bold mb-1" style="font-size: 9px; border-radius: 4px; padding: 4px 8px;">
                                            <i class="bx {{ $tanggalPelatihan->isToday() ? 'bx-play-circle' : 'bx-error-circle' }}"></i>
                                            {{ $tanggalPelatihan->isToday() ? 'HARI INI' : 'TERLEWAT' }}
                                        </span>
                                        @if(auth()->user()->usertype === 'admin')
                                            {{-- Fix: Menggunakan $item->id --}}
                                            <a href="{{ route('mitra.participation.complete', $item->id) }}" class="btn btn-success btn-sm px-2 fw-bold" style="font-size: 10px; border-radius: 6px;">Selesaikan</a>
                                        @endif
                                    @else
                                        <span class="badge bg-warning text-dark fw-bold" style="font-size: 9px; border-radius: 4px; padding: 4px 8px;">
                                            <i class="bx bx-time"></i> AKAN DATANG
                                        </span>
                                    @endif

                                    <div class="d-flex gap-1 mt-1">
                                        <a href="{{ route('mitra.participation.show', $item->id) }}" class="btn btn-light btn-sm border px-2 fw-bold text-dark" style="font-size: 10px; border-radius: 6px;">Detail</a>

                                        @if(auth()->user()->usertype === 'admin')
                                            <button type="button" class="btn btn-light btn-sm border text-primary px-2" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">
                                                <i class="bx bx-edit"></i>
                                            </button>

                                            <form action="{{ route('mitra.participation.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus agenda?')" class="m-0">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-light btn-sm border text-danger px-2" style="border-radius: 6px;"><i class="bx bx-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    @if(auth()->user()->usertype === 'admin')
    <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('mitra.participation.update', $item->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header bg-dark text-white border-0 py-3" style="border-radius: 20px 20px 0 0;">
                        <h6 class="modal-title fw-bold" style="font-size: 14px;"><i class="bx bx-edit me-2"></i>Edit Agenda Silabus</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        <div class="mb-3">
                            <label class="text-label">Judul Pelatihan</label>
                            <input type="text" name="judul_pelatihan" class="form-control form-control-sm" value="{{ $item->judul_pelatihan }}" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="text-label">Tanggal</label>
                                <input type="date" name="tanggal_pelatihan" class="form-control form-control-sm" value="{{ $item->tanggal_pelatihan }}" required>
                            </div>
                            <div class="col-6">
                                <label class="text-label">Waktu</label>
                                <input type="text" name="waktu_pelatihan" class="form-control form-control-sm" value="{{ $item->waktu_pelatihan }}" required>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="text-label">Kategori</label>
                                <select name="kategori" class="form-select form-select-sm">
                                    <option value="Literasi digital" {{ $item->kategori == 'Literasi digital' ? 'selected' : '' }}>Literasi Digital</option>
                                    <option value="Literasi Bisnis" {{ $item->kategori == 'Literasi Bisnis' ? 'selected' : '' }}>Literasi Bisnis</option>
                                    <option value="Literasi Dasar" {{ $item->kategori == 'Literasi Dasar' ? 'selected' : '' }}>Literasi Dasar</option>
                                    <option value="Tematik" {{ $item->kategori == 'Tematik' ? 'selected' : '' }}>Tematik</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="text-label">Pelaksanaan</label>
                                <select name="pelaksanaan" class="form-select form-select-sm">
                                    <option value="online" {{ $item->pelaksanaan == 'online' ? 'selected' : '' }}>Online</option>
                                    <option value="offline" {{ $item->pelaksanaan == 'offline' ? 'selected' : '' }}>Offline</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-label">Narasumber</label>
                            <input type="text" name="narasumber" class="form-control form-control-sm" value="{{ $item->narasumber }}" required>
                        </div>
                        <div class="mb-0">
                            <label class="text-label">Lokasi</label>
                            <input type="text" name="tempat_pelatihan" class="form-control form-control-sm" value="{{ $item->tempat_pelatihan }}" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" class="btn btn-dark w-100 fw-bold py-2 shadow-sm" style="border-radius: 10px;">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    @empty
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body text-center py-5">
            <i class="bx bx-calendar-x text-muted" style="font-size: 48px;"></i>
            <h6 class="mt-3 fw-bold text-muted">Belum ada agenda pelatihan yang dijadwalkan.</h6>
        </div>
    </div>
    @endforelse

    {{-- MODAL INPUT --}}
    @if(auth()->user()->usertype === 'admin')
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('mitra.participation.store') }}" method="POST">
                @csrf
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header bg-primary text-white border-0 py-3" style="border-radius: 20px 20px 0 0;">
                        <h6 class="modal-title fw-bold" style="font-size: 14px;"><i class="bx bx-calendar-plus me-2"></i>Susun Agenda Baru</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        <div class="mb-3"><label class="text-label">Mitra Kerjasama</label>
                            <select name="mitra_id" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Mitra --</option>
                                @foreach($mitras as $mitra)
                                    <option value="{{ $mitra->id }}">{{ $mitra->nama_perusahaan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3"><label class="text-label">Judul Pelatihan</label>
                            <input type="text" name="judul_pelatihan" class="form-control form-control-sm" required placeholder="Judul materi...">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6"><label class="text-label">Tanggal</label>
                                <input type="date" name="tanggal_pelatihan" class="form-control form-control-sm" required></div>
                            <div class="col-6"><label class="text-label">Waktu</label>
                                <input type="text" name="waktu_pelatihan" class="form-control form-control-sm" placeholder="09:00 - 12:00" required></div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6"><label class="text-label">Kategori</label>
                                <select name="kategori" class="form-select form-select-sm" required>
                                    <option value="Literasi digital">Literasi Digital</option>
                                    <option value="Literasi Bisnis">Literasi Bisnis</option>
                                    <option value="Literasi Dasar">Literasi Dasar</option>
                                    <option value="Tematik">Tematik</option>
                                </select></div>
                            <div class="col-6"><label class="text-label">Pelaksanaan</label>
                                <select name="pelaksanaan" class="form-select form-select-sm">
                                    <option value="online">Online</option>
                                    <option value="offline">Offline</option>
                                </select></div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6"><label class="text-label">Narasumber</label>
                                <input type="text" name="narasumber" class="form-control form-control-sm" required></div>
                            <div class="col-6"><label class="text-label">Lokasi</label>
                                <input type="text" name="tempat_pelatihan" class="form-control form-control-sm" required></div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm" style="border-radius: 10px;">Jadwalkan ke Silabus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

