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
                {{-- Tombol Export memicu Modal Preview --}}
                <button type="button" class="btn btn-primary" onclick="openPreviewModal()">
                    <i class="bx bx-export"></i> Export CSV
                </button>
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
                <table class="table table-hover align-middle" id="mainTable">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">Nama Perusahaan</th>
                            <th class="border-0">Bidang Perusahaan</th>
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
                            
                            {{-- UPDATE 1: Menampilkan data dari kolom 'bidang_perusahaan' --}}
                            <td>{{ $mitra->bidang_perusahaan ?? '-' }}</td>
                            
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

{{-- Modal Preview Export (Khusus Admin) --}}
@if($user->usertype == 'admin')
<div class="modal fade" id="exportPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Preview Data Export</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info small mb-3">
                    <i class="bx bx-info-circle"></i> Berikut adalah pratinjau data yang akan diunduh ke file CSV sesuai dengan filter pencarian Anda.
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-striped" id="previewTableContent">
                        <thead class="table-light"></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="btnRealDownload" class="btn btn-success">
                    <i class="bx bx-download"></i> Download CSV Sekarang
                </a>
            </div>
        </div>
    </div>
</div>
@endif

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
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama penanggung jawab" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">No. Telepon PIC</label>
                            <input type="text" name="no_telepon" class="form-control" placeholder="08..." required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Perusahaan</label>
                        <input type="text" name="nama_perusahaan" class="form-control" placeholder="Nama usaha/startup" required>
                    </div>

                    {{-- UPDATE 2: Input name diganti jadi "bidang_perusahaan" agar sesuai Controller & Database --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Bidang Perusahaan</label>
                        <input type="text" name="bidang_perusahaan" class="form-control" placeholder="Contoh: Teknologi, Pendidikan, Kuliner, dll." required>
                    </div>
                    {{-- ==================================================== --}}

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Lokasi Perusahaan</label>
                        <input type="text" name="lokasi_perusahaan" class="form-control" placeholder="Alamat lengkap" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Deskripsi Singkat</label>
                        <textarea name="deskripsi_perusahaan" class="form-control" rows="3" placeholder="Jelaskan singkat tentang usaha Anda..." required></textarea>
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

{{-- Script Javascript Logic --}}
@if($user->usertype == 'admin')
<script>
    function openPreviewModal() {
        var sourceTable = document.getElementById('mainTable');
        var targetHead = document.querySelector('#previewTableContent thead');
        var targetBody = document.querySelector('#previewTableContent tbody');

        if(sourceTable) {
            // Copy Header & Body
            targetHead.innerHTML = sourceTable.querySelector('thead').innerHTML;
            targetBody.innerHTML = sourceTable.querySelector('tbody').innerHTML;

            // Hapus kolom 'Aksi' di header preview
            var headRow = targetHead.querySelector('tr');
            if(headRow && headRow.lastElementChild) headRow.lastElementChild.remove(); 

            // Hapus tombol 'Detail' di body preview
            var bodyRows = targetBody.querySelectorAll('tr');
            bodyRows.forEach(function(row) {
                if(row.lastElementChild) row.lastElementChild.remove(); 
            });

            // Update Link Download dengan filter
            var currentParams = window.location.search; 
            var baseUrl = "{{ route('list_mitra.export') }}";
            document.getElementById('btnRealDownload').href = baseUrl + currentParams;

            // Show Modal
            var myModal = new bootstrap.Modal(document.getElementById('exportPreviewModal'));
            myModal.show();
        }
    }
</script>
@endif

@endsection