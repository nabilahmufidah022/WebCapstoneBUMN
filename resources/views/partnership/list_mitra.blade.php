@extends('layout.index')

@section('content')

<div class="container py-4">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- ================= TAMPILAN KHUSUS ADMIN ================= --}}
    @if($user->usertype == 'admin')
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Pusat Data Mitra</h4>
                <p class="text-muted small mb-0">Kelola dan analisis data kemitraan Rumah BUMN Jakarta secara komprehensif.</p>
            </div>
            <div class="d-flex gap-2">
                {{-- FITUR 1: Ganti Teks Menjadi Tambah Mitra --}}
                <button type="button" class="btn btn-outline-primary rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahMitra">
                    <i class="bx bx-plus me-1"></i> Tambah Mitra
                </button>
                {{-- Button Cetak Laporan --}}
                <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold" onclick="openCetakPreviewModal()">
                    <i class="bx bx-printer me-1"></i> Cetak Laporan
                </button>
            </div>
        </div>

        {{-- Filter & Search Section --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-body p-4">
                <form action="{{ route('list_mitra') }}" method="GET" class="row g-2">
                    <input type="hidden" name="mode" value="{{ request('mode') }}">

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Pencarian</label>
                        <input type="text" name="search" class="form-control form-control-sm shadow-none" placeholder="Nama perusahaan atau PIC..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Bidang Usaha</label>
                        <select name="bidang" class="form-select form-select-sm shadow-none">
                            <option value="">Semua Bidang</option>
                            <option value="Ecommerce" {{ request('bidang') == 'Ecommerce' ? 'selected' : '' }}>Ecommerce</option>
                            <option value="Teknologi" {{ request('bidang') == 'Teknologi' ? 'selected' : '' }}>Teknologi</option>
                            <option value="Kuliner" {{ request('bidang') == 'Kuliner' ? 'selected' : '' }}>Kuliner</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Urutan (Rekomendasi)</label>
                        <select name="sort" class="form-select form-select-sm shadow-none">
                            <option value="">-- Standar --</option>
                            <option value="rating_high" {{ request('sort') == 'rating_high' ? 'selected' : '' }}>Rating Tertinggi ⭐</option>
                            <option value="keaktifan_low" {{ request('sort') == 'keaktifan_low' ? 'selected' : '' }}>Minim Keterlibatan (Non-Aktif)</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Tahun Gabung</label>
                        <select name="tahun" class="form-select form-select-sm shadow-none">
                            <option value="">Pilih Tahun</option>
                            @for($year = 2024; $year <= 2026; $year++)
                                <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-secondary btn-sm w-100 rounded-3 fw-bold">
                            <i class="bx bx-search"></i> Cari Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle border-top" id="mainTable">
                    <thead class="table-light">
                        <tr class="text-nowrap small text-uppercase">
                            <th class="border-0 ps-3">Nama Perusahaan</th>
                            <th class="border-0">Bidang</th>
                            <th class="border-0 text-center">Tahun Gabung</th>
                            <th class="border-0 text-center">Total Keterlibatan</th>
                            <th class="border-0">Status Monitoring</th>
                            <th class="border-0">Rating Avg</th>
                            <th class="border-0 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sortedMitras = $mitras;
                            if(request('sort') == 'keaktifan_low') {
                                // Mengurutkan berdasarkan total partisipasi terkecil secara realtime
                                $sortedMitras = $mitras->sortBy('mitra_event_participations_count');
                            }
                        @endphp

                        @forelse($sortedMitras as $mitra)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-primary">{{ $mitra->nama_perusahaan }}</div>
                                <small class="text-muted"><i class="bx bx-user-circle"></i> {{ $mitra->nama_lengkap }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $mitra->bidang_perusahaan ?? '-' }}</span></td>

                            <td class="text-center fw-bold text-dark">
                                {{ $mitra->created_at->format('Y') }}
                            </td>
                            <td class="text-center">
                                {{-- INTEGRASI REALTIME: Menggunakan mitra_event_participations_count dari withCount --}}
                                <span class="badge bg-light text-primary border px-3">
                                    {{ $mitra->mitra_event_participations_count ?? 0 }} Pelatihan
                                </span>
                            </td>

                            <td>
                                @if($mitra->status_aktif == 'Aktif')
                                    <span class="badge bg-success-soft text-success px-3 rounded-pill fw-bold">
                                        <i class="bx bxs-check-shield me-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-secondary-soft text-muted px-3 rounded-pill fw-bold" title="Keterlibatan tahun ini minim (1-3 kali)">
                                        Non-Aktif
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div class="text-warning fw-bold">
                                    {{ number_format($mitra->average_rating ?? 0, 1) }} <i class="bx bxs-star"></i>
                                </div>
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm fw-bold" onclick="showIdentitasMitra({{ $mitra->id }})">
                                        Detail
                                    </button>

                                    @if(request('mode') == 'selection')
                                    <a href="{{ route('mitra.participation.index', ['mitra_id' => $mitra->id, 'status' => 'Akan Datang']) }}"
                                       class="btn btn-sm btn-success fw-bold px-2 rounded-circle shadow-sm">
                                        <i class="bx bx-check"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bx bx-info-circle fs-2 d-block mb-2"></i>
                                Tidak ada data mitra yang sesuai dengan filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MODAL PREVIEW & FILTER CETAK LAPORAN --}}
        <div class="modal fade" id="modalPreviewCetak" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 bg-light py-3">
                        <h5 class="fw-bold mb-0"><i class="bx bx-printer me-2 text-success"></i>Cetak Laporan Mitra</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        {{-- Form Filter Periode Cetak --}}
                        <form action="{{ route('list_mitra.export') }}" method="GET" id="formCetakLaporan" class="mb-4">
                            <div class="row g-3 p-3 bg-light rounded-3 border">
                                <div class="col-md-12">
                                    <h6 class="fw-bold mb-2 small text-muted text-uppercase">Filter Periode Laporan (Berdasarkan Tgl Gabung)</h6>
                                </div>
                                <div class="col-md-5">
                                    <label class="small fw-bold mb-1">Tahun</label>
                                    <select name="tahun_cetak" class="form-select shadow-none" required>
                                        @for($y = 2024; $y <= 2026; $y++)
                                            <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="small fw-bold mb-1">Bulan (Opsional)</label>
                                    <select name="bulan_cetak" class="form-select shadow-none">
                                        <option value="">-- Semua Bulan --</option>
                                        @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $val => $label)
                                            <option value="{{ $val }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-success w-100 fw-bold shadow-sm">
                                        <i class="bx bxs-file-export me-1"></i> Cetak
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="p-4 text-center border-bottom bg-white mb-3">
                            <h5 class="fw-bold mb-1">PREVIEW DATA SAAT INI</h5>
                            <p class="text-muted small mb-0">Seluruh data mitra terverifikasi yang tampil pada tabel utama.</p>
                        </div>
                        <div class="table-responsive" style="max-height: 300px;">
                            <table class="table table-sm align-middle mb-0 small">
                                <thead class="bg-light sticky-top">
                                    <tr>
                                        <th class="ps-4 py-3">Perusahaan</th>
                                        <th>Bidang</th>
                                        <th>Tgl Gabung</th>
                                        <th class="text-center">Keterlibatan</th>
                                        <th>Status</th>
                                        <th class="pe-4">Rating</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mitras as $m)
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary">{{ $m->nama_perusahaan }}</td>
                                        <td>{{ $m->bidang_perusahaan }}</td>
                                        <td>{{ $m->created_at->format('d/m/Y') }}</td>
                                        <td class="text-center">{{ $m->mitra_event_participations_count ?? 0 }} Sesi</td>
                                        <td>{{ $m->status_aktif }}</td>
                                        <td class="pe-4 text-warning fw-bold">{{ number_format($m->average_rating ?? 0, 1) }} ⭐</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-3 bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL TAMBAH MITRA --}}
        <div class="modal fade" id="modalTambahMitra" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <form action="{{ route('mitra.storeManual') }}" method="POST">
                        @csrf
                        <div class="modal-header border-0 pb-0">
                            <h5 class="fw-bold"><i class="bx bx-plus-circle text-primary me-2"></i>Tambah Mitra Baru</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="small fw-bold text-muted mb-1">NAMA PERUSAHAAN</label>
                                <input type="text" name="nama_perusahaan" class="form-control rounded-3" placeholder="Contoh: PT. Maju Jaya" required>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold text-muted mb-1">BIDANG USAHA</label>
                                <input type="text" name="bidang_perusahaan" class="form-control rounded-3" placeholder="Contoh: Kuliner / Kerajinan" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="small fw-bold text-muted mb-1">NAMA PIC</label>
                                    <input type="text" name="nama_lengkap" class="form-control rounded-3" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="small fw-bold text-muted mb-1">NOMOR TELEPON</label>
                                    <input type="text" name="no_telepon" class="form-control rounded-3" placeholder="08xxxxxxxx" required>
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="small fw-bold text-muted mb-1">ALAMAT PERUSAHAAN</label>
                                <textarea name="lokasi_perusahaan" class="form-control rounded-3" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold">Simpan & Aktifkan Mitra</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @else
        {{-- Tampilan untuk User/Mitra --}}
    @endif
</div>

<style>
    .bg-success-soft { background-color: #e6f4ea; color: #1e7e34; border: 1px solid #c3e6cb; }
    .bg-secondary-soft { background-color: #f8f9fa; color: #6c757d; border: 1px solid #dee2e6; }

    #mainTable thead th {
        font-weight: 700;
        color: #495057;
        background-color: #f8f9fc;
        padding-top: 15px;
        padding-bottom: 15px;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f4f9;
        transition: 0.2s ease;
    }

    .modal-content {
        animation: modalFadeIn 0.3s ease-out;
    }

    @keyframes modalFadeIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>

@if($user->usertype == 'admin')
<script>
    function openCetakPreviewModal() {
        var myModal = new bootstrap.Modal(document.getElementById('modalPreviewCetak'));
        myModal.show();
    }

    function showIdentitasMitra(id) {
        window.location.href = "/mitra/detail-identitas/" + id;
    }
</script>
@endif

@endsection
