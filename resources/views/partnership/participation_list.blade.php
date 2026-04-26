@extends('layout.index')

@section('content')
<div class="container mt-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                {{ request('status') == 'Selesai' ? 'Histori Kerja Sama' : 'Silabus Pelatihan' }}
            </h4>
            <p class="text-muted mb-0">Kelola agenda pelatihan yang terintegrasi dengan Pusat Data Mitra.</p>
        </div>

        @if(auth()->user()->usertype === 'admin' && request('status') != 'Selesai')
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bx bx-plus-circle me-1"></i> Susun Silabus Baru
        </button>
        @endif
    </div>

    {{-- Filter & Search --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body">
            <form class="row g-3" method="GET">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <div class="col-md-5">
                    <label class="form-label small fw-bold">Cari Agenda</label>
                    <input type="text" name="search" class="form-control" placeholder="Judul pelatihan atau narasumber..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Jenis Pelatihan</label>
                    <select name="kategori" class="form-select">
                        <option value="">-- Semua Jenis --</option>
                        <option value="Literasi digital">Literasi Digital</option>
                        <option value="Literasi Bisnis">Literasi Bisnis</option>
                        <option value="Literasi Dasar">Literasi Dasar</option>
                        <option value="Tematik">Tematik</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Utama --}}
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle border-top">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th>Tanggal & Waktu</th>
                        <th>Kelas & Lokasi</th>
                        <th>Judul Pelatihan</th>
                        <th>Kategori</th>
                        <th>Mitra Kerjasama</th>
                        <th>Narasumber</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participations as $item)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ \Carbon\Carbon::parse($item->tanggal_pelatihan)->format('d/m/Y') }}</div>
                            <div class="small text-muted">{{ $item->waktu_pelatihan }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $item->pelaksanaan == 'online' ? 'bg-danger' : 'bg-info text-dark' }} mb-1">
                                {{ ucfirst($item->pelaksanaan) }}
                            </span>
                            <div class="small fw-bold">{{ $item->tempat_pelatihan }}</div>
                        </td>
                        <td class="small" style="max-width: 200px;">{{ $item->judul_pelatihan }}</td>
                        <td><span class="badge bg-secondary">{{ $item->kategori }}</span></td>
                        <td class="fw-bold text-primary">{{ $item->mitra->nama_perusahaan ?? '-' }}</td>
                        <td class="small">{{ $item->narasumber }}</td>
                        <td class="text-center">
                            @if(auth()->user()->usertype === 'admin' && $item->status == 'Akan Datang')
                                <form action="{{ route('mitra.participation.complete', $item->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-success btn-sm w-100 mb-1 rounded-pill">Selesaikan</button>
                                </form>
                            @endif
                            <a href="{{ route('mitra.participation.show', $item->id) }}" class="btn btn-outline-primary btn-sm w-100 rounded-pill">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada data tersedia.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL INPUT SILABUS --}}
@if(auth()->user()->usertype === 'admin')
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('mitra.participation.store') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bx bx-calendar-plus me-2"></i>Susun Silabus Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Pilih Mitra Kerjasama</label>
                        <div class="input-group">
                            <select name="mitra_id" id="mitra_select" class="form-select border-primary" required>
                                <option value="">-- Pilih dari Pusat Data --</option>
                                @foreach($mitras as $mitra)
                                    <option value="{{ $mitra->id }}">{{ $mitra->nama_perusahaan }}</option>
                                @endforeach
                            </select>
                            <a href="{{ route('list_mitra') }}" class="btn btn-outline-primary">
                                <i class="bx bx-search"></i> Cari di Pusat Data
                            </a>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Judul Pelatihan</label>
                        <input type="text" name="judul_pelatihan" class="form-control" required placeholder="Contoh: Digital Marketing UMKM">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase">Tanggal</label>
                            <input type="date" name="tanggal_pelatihan" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase">Waktu</label>
                            <input type="text" name="waktu_pelatihan" class="form-control" placeholder="09:00 - 12:00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase">Narasumber</label>
                            <input type="text" name="narasumber" class="form-control" required placeholder="Nama Pemateri">
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">Lokasi (Tempat/Link)</label>
                            <input type="text" name="tempat_pelatihan" class="form-control" placeholder="Contoh: Rumah BUMN Jakarta / Zoom" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-uppercase">Kategori Pelatihan</label>
                            <select name="kategori" id="kategori_select" class="form-select">
                                <option value="Literasi digital">Literasi Digital</option>
                                <option value="Literasi Bisnis">Literasi Bisnis</option>
                                <option value="Literasi Dasar">Literasi Dasar</option>
                                <option value="Tematik">Tematik</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-uppercase">Kelas</label>
                            <select name="pelaksanaan" class="form-select">
                                <option value="online">Online</option>
                                <option value="offline">Offline</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan ke Silabus</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

{{-- JAVASCRIPT LOGIC --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Logika 1: Cek URL Parameter
        // Jika Admin datang dari halaman Pusat Data Mitra dengan link: ...?mitra_id=5
        const urlParams = new URLSearchParams(window.location.search);
        const mitraIdParam = urlParams.get('mitra_id');
        
        if (mitraIdParam) {
            // 1. Pilih mitra otomatis di dropdown
            const selectMitra = document.getElementById('mitra_select');
            if (selectMitra) {
                selectMitra.value = mitraIdParam;
                
                // 2. Tampilkan modal secara otomatis
                const addModal = new bootstrap.Modal(document.getElementById('addModal'));
                addModal.show();
            }
        }
    });
</script>

@endsection