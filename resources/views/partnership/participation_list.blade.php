@extends('layout.index')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">Keikutsertaan Mitra</h4>
            <p class="text-muted mb-0">
                Daftar keikutsertaan mitra dalam kegiatan pelatihan
            </p>
        </div>

        @if(auth()->user()->usertype === 'admin')
        <div class="d-flex gap-2">
            <a href="#"
                class="btn btn-primary rounded-pill px-4"
                data-bs-toggle="modal"
                data-bs-target="#addParticipationModal">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Keikutsertaan
            </a>

            <a href="{{ route('mitra.participation.export', request()->query()) }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-download me-1"></i> Export CSV
            </a>
        </div>
        @endif
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body border-bottom">
            <form class="row g-2" method="GET" action="{{ route('mitra.participation.index') }}">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control uniform-input" placeholder="Cari judul atau nama mitra"
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <select name="kategori" class="form-select uniform-input">
                        <option value="">-- Semua Kategori --</option>
                        <option value="Literasi digital" {{ request('kategori') == 'Literasi digital' ? 'selected' : '' }}>Literasi digital</option>
                        <option value="Literasi Bisnis" {{ request('kategori') == 'Literasi Bisnis' ? 'selected' : '' }}>Literasi Bisnis</option>
                        <option value="Literasi Dasar" {{ request('kategori') == 'Literasi Dasar' ? 'selected' : '' }}>Literasi Dasar</option>
                        <option value="Tematik" {{ request('kategori') == 'Tematik' ? 'selected' : '' }}>Tematik</option>
                    </select>
                </div>

                <div class="col-md-2">
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

    <div class="card border-0 shadow-sm " style="border-radius: 12px;">
        <div class="table-responsive p-3">
            <table class="table align-middle mb-0 participation-table">
                <thead class="table-light">
                    <tr>
                        <th class="border-0">Nama Perusahaan</th>
                        <th class="border-0">Bidang</th>
                        <th class="border-0">Judul Pelatihan</th>
                        <th class="border-0">Tanggal Pelatihan</th>
                        <th class="border-0">Tempat</th>
                        <th class="border-0">Narasumber</th>
                        <th class="border-0">Pelaksanaan</th>
                        <th class="text-center border-0">Action</th>
                    </>
                </thead>

                <tbody>
                    @forelse($participations as $item)
                    <tr>
                        <td class="fw-bold text-primary"> {{ $item->mitra->nama_perusahaan }} </td>
                        <td> {{ $item->kategori }} </td>
                        <td> {{ $item->judul_pelatihan }} </td>
                        <td> {{ \Carbon\Carbon::parse($item->tanggal_pelatihan)->translatedFormat('d F Y') }} </td>
                        <td> {{ $item->tempat_pelatihan }} </td>
                        <td> {{ $item->narasumber }}</td>
                        <td> {{$item->status}} </td>
                        <td class="text-center">
                            <a href="{{ route('mitra.participation.show', $item->id) }}"
                               class="btn btn-light btn-sm rounded-pill text-primary text-decoration-none fw-semibold">
                                <i class="bi bi-eye me-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data keikutsertaan mitra
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL FORM -->
<div class="modal fade" id="addParticipationModal" tabindex="-1" aria-labelledby="addParticipationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 15px;">

            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="addParticipationModalLabel">
                    Form Keikutsertaan Mitra
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4">
                <form action="{{ route('mitra.participation.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Mitra</label>
                        <select name="mitra_id" class="form-select" required>
                            <option value="">-- Pilih Mitra --</option>
                            @foreach($mitras as $mitra)
                                @if($mitra->status == 1)
                                    <option value="{{ $mitra->id }}">
                                        {{ $mitra->nama_perusahaan }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Judul Pelatihan</label>
                        <input type="text" name="judul_pelatihan" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Tanggal Pelatihan</label>
                            <input type="date" name="tanggal_pelatihan" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Waktu Pelatihan</label>
                            <input type="time" name="waktu_pelatihan" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Tempat Pelatihan</label>
                        <input type="text" name="tempat_pelatihan" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Narasumber</label>
                        <input type="text" name="narasumber" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Literasi digital">Literasi Digital</option>
                                <option value="Literasi Bisnis">Literasi Bisnis</option>
                                <option value="Literasi Dasar">Literasi Dasar</option>
                                <option value="Tematik">Tematik</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Pelaksanaan</label>
                            <select name="status" class="form-select" required>
                                <option value="">-- Pilih Pelaksanaan --</option>
                                <option value="online">Online</option>
                                <option value="offline">Offline</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer border-0 px-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>


{{-- Auto open modal if validation error --}}
@if ($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function () {
        new bootstrap.Modal(document.getElementById('addParticipationModal')).show();
    });
</script>
@endif
@endsection
