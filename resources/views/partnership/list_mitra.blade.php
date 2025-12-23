@extends('layout.index')

@section('content')

<div class="container py-4">

    @if(session('success')) 
    <div class="alert alert-success alert-dismissible fade show" role="alert"> 
        {{ session('success') }} 
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div> 
    @endif 

    {{-- ================= TAMPILAN KHUSUS ADMIN ================= --}}
    @if($user->usertype == 'admin')
        <div class="d-flex justify-content-between align-items-center mb-4"> 
            <h4 class="fw-bold">Database Mitra Aktif</h4>
            <div>
                <a href="{{ route('list_mitra.export', request()->query()) }}" class="btn btn-primary">
                    <i class="bx bx-export"></i> Export CSV
                </a>
            </div>
        </div>

        {{-- Filter dan Search Section --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body">
                <form action="{{ route('list_mitra') }}" method="GET" class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label small fw-bold">Pencarian</label>
                        <input type="text" name="search" class="form-control uniform-input" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                    </div>
        
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Tahun</label>
                        <select name="tahun" class="form-select uniform-input">
                            <option value="">Semua Tahun</option>
                            @for($year = 2022; $year <= 2025; $year++)
                                <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="bx bx-filter-alt"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">Nama Perusahaan</th>
                            <th class="border-0">Bidang</th>
                            <th class="border-0">Lokasi</th>
                            <th class="border-0">PIC Mitra</th>
                            <th class="border-0">Email</th>
                            <th class="border-0">No. Telepon PIC</th>
                            <th class="border-0 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mitras as $mitra)
                        <tr>
                            <td class="fw-bold text-primary">{{ $mitra->nama_perusahaan }}</td>
                            <td>{{ $mitra->bidang_usaha ?? '-' }}</td>
                            <td><i class="bx bx-map"></i> {{ $mitra->lokasi_perusahaan }}</td>
                            <td>{{ $mitra->nama_lengkap }}</td>
                            <td class="small">{{ $mitra->user->email ?? '-' }}</td>
                            <td>{{ $mitra->no_telepon }}</td>
                            <td class="text-center">
                                <a href="{{ route('mitra.detail', $mitra->id) }}" class="btn btn-light btn-sm rounded-pill text-primary text-decoration-none fw-semibold">
                                    <i class="bx bx-show"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada mitra yang disetujui.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    {{-- ================= TAMPILAN KHUSUS USER ================= --}}
    @else
        <div class="d-flex justify-content-between align-items-center mb-4"> 
            <h4 class="fw-bold">Status Pendaftaran Mitra Anda</h4>
            @if(!$hasMitra)
                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#daftarMitraModal">
                    <i class="bx bx-plus"></i> Daftar Mitra Baru
                </button> 
            @endif
        </div>

        @forelse($mitras as $mitra)
            <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $mitra->nama_perusahaan }}</h5>
                            <div class="text-muted small">
                                <i class="bx bx-calendar"></i> Diajukan pada {{ \Carbon\Carbon::parse($mitra->created_at)->translatedFormat('d F Y') }}
                            </div>
                        </div>
                        <div>
                            @if($mitra->status == 0)
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Sedang Ditinjau (Pending)</span>
                            @elseif($mitra->status == 1)
                                <span class="badge bg-success px-3 py-2 rounded-pill">Disetujui</span>
                            @elseif($mitra->status == 2)
                                <span class="badge bg-danger px-3 py-2 rounded-pill">Ditolak</span>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row small text-muted">
                        <div class="col-md-4"><i class="bx bx-user"></i> PIC: {{ $mitra->nama_lengkap }}</div>
                        <div class="col-md-4"><i class="bx bx-phone"></i> {{ $mitra->no_telepon }}</div>
                        <div class="col-md-4 text-md-end">
                            <a href="{{ route('mitra.detail', $mitra->id) }}" class="btn btn-link btn-sm p-0 text-decoration-none text-primary">Lihat Detail Pengajuan →</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 bg-white shadow-sm rounded-4 mt-4">
                <i class="bi bi-patch-question fs-1 text-muted"></i>
                <p class="mt-3 text-muted">Anda belum memiliki riwayat pendaftaran mitra.</p>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#daftarMitraModal">Mulai Daftar Sekarang</button>
            </div>
        @endforelse
    @endif
</div>

{{-- Modal Daftar Mitra (Tetap ada untuk User) --}}
@if($user->usertype != 'admin' && !$hasMitra)
<div class="modal fade" id="daftarMitraModal" tabindex="-1" aria-labelledby="daftarMitraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="daftarMitraModalLabel">Form Pendaftaran Mitra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4">
                <form action="{{ route('store_mitra') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Nama Lengkap PIC</label>
                            <input type="text" name="nama_lengkap" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">No. Telepon PIC</label>
                            <input type="text" name="no_telepon" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Perusahaan</label>
                        <input type="text" name="nama_perusahaan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Lokasi Perusahaan</label>
                        <input type="text" name="lokasi_perusahaan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Deskripsi Singkat</label>
                        <textarea name="deskripsi_perusahaan" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Company Profile (PDF)</label>
                            <input type="file" name="company_profile" class="form-control" accept=".pdf">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Surat Audiensi (PDF)</label>
                            <input type="file" name="surat_permohonan_audiensi" class="form-control" accept=".pdf">
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim Pendaftaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
