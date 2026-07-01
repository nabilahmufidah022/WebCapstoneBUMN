@extends('layout.index')

@section('content')

<div class="container-xl py-3">
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
                <form action="{{ route('list_mitra') }}" method="GET" class="row g-2 align-items-end">
                    <input type="hidden" name="mode" value="{{ request('mode') }}">

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted mb-1">Pencarian</label>
                        <input type="text" name="search" class="form-control form-control-sm shadow-none" placeholder="Nama perusahaan atau PIC..." value="{{ request('search') }}">
                    </div>

                    {{-- FITUR REVISI DOSEN: SELECTION FILTER DROPDOWN KATEGORI PELATIHAN --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted mb-1">Bidang Usaha</label>
                        <select name="bidang" class="form-select form-select-sm shadow-none">
                            <option value="">Semua Bidang</option>
                            <option value="Literasi Digital" {{ request('bidang') == 'Literasi Digital' ? 'selected' : '' }}>Literasi Digital</option>
                            <option value="Literasi Bisnis" {{ request('bidang') == 'Literasi Bisnis' ? 'selected' : '' }}>Literasi Bisnis</option>
                            <option value="Literasi Dasar" {{ request('bidang') == 'Literasi Dasar' ? 'selected' : '' }}>Literasi Dasar</option>
                            <option value="Tematik" {{ request('bidang') == 'Tematik' ? 'selected' : '' }}>Tematik</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted mb-1">Urutan (Rekomendasi)</label>
                        <select name="sort" class="form-select form-select-sm shadow-none">
                            <option value="">-- Standar --</option>
                            <option value="rating_high" {{ request('sort') == 'rating_high' ? 'selected' : '' }}>Rating Tertinggi ⭐</option>
                            <option value="keaktifan_low" {{ request('sort') == 'keaktifan_low' ? 'selected' : '' }}>Minim Keterlibatan (Non-Aktif)</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted mb-1">Tahun Gabung</label>
                        <select name="tahun" class="form-select form-select-sm shadow-none">
                            <option value="">Pilih Tahun</option>
                            @for($year = 2024; $year <= 2026; $year++)
                                <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary btn-sm w-100 rounded-3 fw-bold py-1.5">
                            <i class="bx bx-search"></i> Cari Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle border-top mb-0" id="mainTable">
                    <thead class="table-light">
                        <tr class="small text-uppercase text-muted fw-bold">
                            <th class="border-0 ps-3">Nama Perusahaan</th>
                            <th class="border-0">Bidang</th>
                            <th class="border-0 text-center">Tahun Gabung</th>
                            <th class="border-0 text-center">Total Keterlibatan</th>
                            <th class="border-0 text-center">Status Monitoring</th>
                            <th class="border-0 text-center">Rating Avg</th>
                            <th class="border-0 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sortedMitras = $mitras;
                            if(request('sort') == 'keaktifan_low') {
                                $sortedMitras = $mitras->sortBy('mitra_event_participations_count');
                            }
                        @endphp

                        @forelse($sortedMitras as $mitra)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-primary">{{ $mitra->nama_perusahaan }}</div>
                                <small class="text-muted"><i class="bx bx-user-circle"></i> {{ $mitra->nama_lengkap }}</small>
                            </td>

                            {{-- FITUR REVISI DOSEN: RENDER MULTIPLE BADGES KATEGORI DI MAIN TABLE --}}
                            <td>
                                @if(is_array($mitra->bidang_perusahaan) && count($mitra->bidang_perusahaan) > 0)
                                    <div class="d-flex flex-wrap gap-1" style="max-width: 250px;">
                                        @foreach($mitra->bidang_perusahaan as $bidang)
                                            <span class="badge bg-light text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill small fw-bold">
                                                {{ $bidang }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            <td class="text-center fw-bold text-dark">
                                {{ $mitra->created_at->format('Y') }}
                            </td>

                            <td class="text-center">
                                <span class="badge bg-light text-primary border px-3 py-1">
                                    {{ $mitra->mitra_event_participations_count ?? 0 }} Pelatihan
                                </span>
                            </td>

                            <td class="text-center">
                                @if($mitra->status_aktif == 'Aktif')
                                    <span class="badge bg-success-soft text-success px-3 rounded-pill fw-bold">
                                        <i class="bx bxs-check-shield me-1"></i> Profil Aktif
                                    </span>
                                @else
                                    <span class="badge bg-secondary-soft text-muted px-3 rounded-pill fw-bold">
                                        Non-Aktif
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
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

        {{-- ==========================================================================
             🌟 MODAL PREVIEW & FILTER CETAK LAPORAN (VERSI COMPACT & HEMAT RUANG LAYAR)
             ========================================================================== --}}
        <div class="modal fade" id="modalPreviewCetak" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered my-2">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 bg-light py-2 px-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bx bx-printer me-2 text-success fs-5"></i>Cetak Laporan Mitra</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 12px;"></button>
                    </div>
                    <div class="modal-body p-3">
                        {{-- Form Filter Periode --}}
                        <form action="{{ route('list_mitra.export') }}" method="GET" id="formCetakLaporan" class="mb-3">
                            <div class="row g-2 p-2.5 bg-light rounded-3 border align-items-end">
                                <div class="col-md-12 mb-1">
                                    <h6 class="fw-bold mb-0 text-muted text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">Filter Periode Laporan (Berdasarkan Tgl Gabung)</h6>
                                </div>
                                <div class="col-md-5">
                                    <label class="fw-bold text-dark mb-1" style="font-size: 11px;">Tahun</label>
                                    <select name="tahun_cetak" class="form-select form-select-sm shadow-none" required>
                                        @for($y = 2024; $y <= 2026; $y++)
                                            <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="fw-bold text-dark mb-1" style="font-size: 11px;">Bulan (Opsional)</label>
                                    <select name="bulan_cetak" class="form-select form-select-sm shadow-none">
                                        <option value="">-- Semua Bulan --</option>
                                        @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $val => $label)
                                            <option value="{{ $val }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success btn-sm w-100 fw-bold shadow-sm py-1.5" style="background-color: #198754; border-color: #198754;">
                                        <i class="bx bx-printer me-1"></i> Cetak
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="text-center mb-2">
                            <h6 class="fw-bold text-dark mb-0.5" style="letter-spacing: -0.3px; font-size: 14px;">PREVIEW DATA SAAT INI</h6>
                            <p class="text-muted mb-0" style="font-size: 11px;">Seluruh data mitra terverifikasi yang tampil pada tabel utama.</p>
                        </div>
                        
                        {{-- Tabel Preview Dengan Scrollbar Internal Keketatan Max-Height 230px --}}
                        <div class="table-responsive rounded-3 border" style="max-height: 230px; overflow-y: auto;">
                            <table class="table table-sm table-hover align-middle mb-0" style="font-size: 12px;">
                                <thead class="table-light text-secondary fw-bold sticky-top" style="font-size: 11px; letter-spacing: 0.5px; z-index: 1;">
                                    <tr>
                                        <th class="ps-3 py-2 border-0">PERUSAHAAN</th>
                                        <th class="border-0">BIDANG USAHA</th>
                                        <th class="border-0">TGL GABUNG</th>
                                        <th class="border-0 text-center">KETERLIBATAN</th>
                                        <th class="border-0">STATUS</th>
                                        <th class="pe-3 border-0 text-center">RATING</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($mitras as $m)
                                    <tr>
                                        <td class="ps-3 py-1.5">
                                            <div class="fw-bold text-dark" style="line-height: 1.2;">{{ $m->nama_perusahaan }}</div>
                                            <span class="text-muted" style="font-size: 10px;">PIC: {{ $m->nama_lengkap }}</span>
                                        </td>
                                        <td class="text-muted" style="font-size: 11px;">
                                            @if(is_array($m->bidang_perusahaan))
                                                {{ implode(', ', $m->bidang_perusahaan) }}
                                            @else
                                                {{ $m->bidang_perusahaan ?? '-' }}
                                            @endif
                                        </td>
                                        <td>{{ $m->created_at->format('d M Y') }}</td>
                                        <td class="text-center fw-bold text-primary">{{ $m->mitra_event_participations_count ?? 0 }} Sesi</td>
                                        <td>
                                            <span class="badge rounded-pill px-2 py-0.5" style="font-size: 9px; background-color: {{ $m->status_aktif == 'Aktif' ? '#e6f4ea' : '#fce8e6' }}; color: {{ $m->status_aktif == 'Aktif' ? '#137333' : '#c5221f' }};">
                                                {{ $m->status_aktif ?? 'Non-Aktif' }}
                                            </span>
                                        </td>
                                        <td class="pe-3 text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-0.5 fw-bold text-warning" style="font-size: 11px;">
                                                <i class="bx bxs-star"></i>
                                                <span class="text-dark">{{ number_format($m->average_rating ?? 0, 1) }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted small">Tidak ada data mitra terverifikasi untuk ditampilkan.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-2 bg-light rounded-bottom-4 justify-content-end">
                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
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

                            {{-- FITUR REVISI DOSEN: MULTISELECT CHECKBOX PADA MODAL ENTRI MANUAL ADMIN --}}
                            <div class="mb-3">
                                <label class="small fw-bold text-muted mb-2">BIDANG USAHA / KATEGORI PELATIHAN</label>
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="bidang_perusahaan[]" value="Literasi Digital" id="add_digital">
                                        <label class="form-check-label small fw-semibold" for="add_digital">Literasi Digital</label>
                                    </div>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="bidang_perusahaan[]" value="Literasi Bisnis" id="add_bisnis">
                                        <label class="form-check-label small fw-semibold" for="add_bisnis">Literasi Bisnis</label>
                                    </div>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="bidang_perusahaan[]" value="Literasi Dasar" id="add_dasar">
                                        <label class="form-check-label small fw-semibold" for="add_dasar">Literasi Dasar</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="bidang_perusahaan[]" value="Tematik" id="add_tematik">
                                        <label class="form-check-label small fw-semibold" for="add_tematik">Tematik</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="small fw-bold text-muted mb-1">NAMA PIC</label>
                                    <input type="text"
                                           name="nama_lengkap"
                                           class="form-control rounded-3"
                                           placeholder="Nama Penanggung Jawab"
                                           pattern="^[a-zA-Z\s]+$"
                                           title="Nama PIC hanya boleh berisi susunan huruf dan spasi (tanpa angka)"
                                           oninput="this.value = this.value.replace(/[0-9]/g, '')"
                                           required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="small fw-bold text-muted mb-1">NOMOR TELEPON</label>
                                    <input type="tel"
                                           name="no_telepon"
                                           class="form-control rounded-3"
                                           placeholder="08xxxxxxxx"
                                           pattern="^[0-9]+$"
                                           title="Nomor telepon wajib berupa susunan angka numerik murni"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                           required>
                                </div>
                            </div>
                            <div class="mb-3">
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

    // Perbaikan Routing detail agar mengarah secara konsisten ke controller
    function showIdentitasMitra(id) {
        window.location.href = "/mitra/detail-identitas/" + id;
    }
</script>
@endif

@endsection
