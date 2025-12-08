@extends('layout.index')

@section('content')

<div class="container py-4">

    @if(session('success')) 
    <div class="alert alert-success"> 
        {{ session('success') }} 
    </div> 
    @endif 

    
    <div class="d-flex justify-content-between align-items-center mb-4"> 
        <button type="button" class="btn btn-primary" @if($hasMitra) disabled @endif data-bs-toggle="modal" data-bs-target="#daftarMitraModal" @if($hasMitra) title="Anda sudah mendaftar mitra" @endif>Daftar Mitra Baru</button> 
    </div>

    @if($user->usertype == 'admin')
    {{-- Filter dan Search --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="col-md-6">
            <input type="text" class="form-control" placeholder="Cari nama atau email mitra...">
        </div>
        <div class="col-md-3">
            <select class="form-select">
                <option value="pending" selected>Pending</option>
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
            </select>
        </div>
    </div>
    @endif

 
    @foreach($mitras as $mitra)
    <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px;">
        <div class="card-body">

            {{-- Bagian header: Nama + Status --}}
            <div class="d-flex align-items-center mb-2">
                <h5 class="fw-bold mb-0 me-2">{{ $mitra->nama_perusahaan }}</h5>
                @if($mitra->status == 0)
                    <span class="badge bg-warning text-dark">Pending</span>
                @elseif($mitra->status == 1)
                    <span class="badge bg-success">Disetujui</span>
                @elseif($mitra->status == 2)
                    <span class="badge bg-danger">Ditolak</span>
                @endif
            </div>

            {{-- Email, Telepon, dan Kategori --}}
            <div class="text-muted small mb-2">
                <i class="bx bx-envelope"></i> {{ $mitra->user->email ?? '-' }} &nbsp;&nbsp;
                <i class="bx bx-phone"></i> {{ $mitra->no_telepon ?? '-' }} &nbsp;&nbsp;
            </div>

            {{-- Deskripsi singkat --}}
            <p class="mb-2" style="font-size: 14px;">
                {{ Str::limit($mitra->deskripsi_perusahaan, 200) }}
            </p>

            {{-- Tanggal daftar --}}
            <small class="text-muted d-block mb-3" style="font-size: 12px;">
                <i class="bx bx-calendar"></i> Mendaftar pada {{ \Carbon\Carbon::parse($mitra->created_at)->translatedFormat('d F Y') }}
            </small>

            {{-- Tombol aksi (diletakkan di bawah) --}}
            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('mitra.detail', $mitra->id) }}" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="bx bx-show"></i> Detail
                </a>

                @if($user->usertype == 'admin' && $mitra->status == 0)
                <form action="{{ route('mitra.approve', $mitra->id) }}" method="POST" class="me-2">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bx bx-check-circle"></i> Setujui
                    </button>
                </form>

                <form action="{{ route('mitra.reject', $mitra->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bx bx-x-circle"></i> Tolak
                    </button>
                </form>
                @endif
            </div>

        </div>
    </div>
    @endforeach


  {{-- Jika belum ada data --}}
  @if($mitras->isEmpty())
      <div class="text-center text-muted py-5">
          <i class="bi bi-inbox fs-1"></i>
          <p class="mt-3">Belum ada mitra yang terdaftar.</p>
      </div>
  @endif
</div>

<!-- Modal -->
<div class="modal fade" id="daftarMitraModal" tabindex="-1" aria-labelledby="daftarMitraModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="daftarMitraModalLabel">Daftar Mitra Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('store_mitra') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group mb-3">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="no_telepon">No. Telepon</label>
                <input type="text" id="no_telepon" name="no_telepon" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="nama_perusahaan">Nama Perusahaan</label>
                <input type="text" id="nama_perusahaan" name="nama_perusahaan" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="lokasi_perusahaan">Lokasi Perusahaan</label>
                <input type="text" id="lokasi_perusahaan" name="lokasi_perusahaan" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="deskripsi_perusahaan">Deskripsi Singkat Perusahaan</label>
                <textarea id="deskripsi_perusahaan" name="deskripsi_perusahaan" class="form-control" rows="4" required></textarea>
            </div>

            <div class="form-group mb-3">
                <label for="company_profile">Dokumen Company Profile</label>
                <input type="file" id="company_profile" name="company_profile" class="form-control" accept=".pdf,.doc,.docx">
                <small class="form-text text-muted">Format: PDF, DOC, DOCX. Maksimal 5MB.</small>
            </div>

            <div class="form-group mb-3">
                <label for="surat_permohonan_audiensi">Surat Permohonan Audiensi</label>
                <input type="file" id="surat_permohonan_audiensi" name="surat_permohonan_audiensi" class="form-control" accept=".pdf,.doc,.docx">
                <small class="form-text text-muted">Format: PDF, DOC, DOCX. Maksimal 5MB.</small>
            </div>

            <button type="submit" class="btn btn-primary">Daftar Mitra</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
