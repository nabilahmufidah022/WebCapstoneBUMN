@extends('layout.index')

@section('content')
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="fw-bold mb-0 d-flex align-items-center gap-2">
        Detail Mitra
      </h3>

      <small class="text-muted">
        Informasi lengkap data mitra dan riwayat approval
      </small>
    </div>

     @if($mitra->status == 0)
          <span class="status-badge pending">
            <i class="bi bi-clock-history"></i> Pending
          </span>
        @elseif($mitra->status == 1)
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm bg-green-100 text-green-700">
            <i class="bi bi-check-circle-fill"></i> Disetujui
          </span>
        @else
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm bg-red-100 text-red-700">
            <i class="bi bi-x-circle-fill"></i> Ditolak
          </span>
        @endif

    <a href="{{ route('list_mitra') }}" class="btn btn-light">
      ← Kembali
    </a>
  </div>

<div class="row g-4">

    {{-- KIRI : INFORMASI MITRA --}}
  <div class="col-md-8">

    <div class="card custom-card mb-4">
      <div class="card-header custom-card-header">
        <h6 class="mb-0 fw-semibold">Informasi Mitra</h6>
      </div>

      <div class="card-body">
        <div class="row g-4">

          <div class="col-md-6 info-item">
            <div class="icon bg-primary-soft">
              <i class="bi bi-building"></i>
            </div>
            <div>
              <small class="text-muted">Nama Mitra</small>
              <div class="fw-semibold">{{ $mitra->nama_perusahaan }}</div>
            </div>
          </div>

          <div class="col-md-6 info-item">
            <div class="icon bg-purple-soft">
              <i class="bi bi-person"></i>
            </div>
            <div>
              <small class="text-muted">Nama Pemilik</small>
              <div class="fw-semibold">{{ $mitra->nama_lengkap }}</div>
            </div>
          </div>

          <div class="col-md-6 info-item">
            <div class="icon bg-success-soft">
              <i class="bi bi-envelope"></i>
            </div>
            <div>
              <small class="text-muted">Email</small>
              <div class="fw-semibold">{{ $mitra->user->email }}</div>
            </div>
          </div>

          <div class="col-md-6 info-item">
            <div class="icon bg-warning-soft">
              <i class="bi bi-telephone"></i>
            </div>
            <div>
              <small class="text-muted">Telepon</small>
              <div class="fw-semibold">{{ $mitra->no_telepon }}</div>
            </div>
          </div>

          <div class="col-6 info-item">
            <div class="icon bg-secondary-soft">
              <i class="bi bi-geo-alt"></i>
            </div>
            <div>
              <small class="text-muted">Alamat</small>
              <div class="fw-semibold">{{ $mitra->lokasi_perusahaan }}</div>
            </div>
          </div>

          <div class="col-md-6 info-item">
            <div class="icon bg-info-soft">
              <i class="bi bi-calendar-event"></i>
            </div>
            <div>
              <small class="text-muted">Tanggal Pengajuan</small>
              <div class="fw-semibold">
                {{ \Carbon\Carbon::parse($mitra->created_at)->translatedFormat('d F Y') }}
              </div>
            </div>
          </div>

          <div class="col-md-6 info-item">
            <div class="icon bg-green-soft">
              <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div>
              <small class="text-muted">Company Profile</small>
              <div class="fw-semibold">
                <a href="{{ asset('uploads/mitra/' . $mitra->company_profile) }}" target="_blank">Download</a>
              </div>
            </div>
          </div>

          <div class="col-md-6 info-item">
            <div class="icon bg-green-soft">
              <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div>
              <small class="text-muted">Surat Permohonan Audiensi:</small>
              <div class="fw-semibold">
                <a href="{{ asset('uploads/mitra/' . $mitra->surat_permohonan_audiensi) }}" target="_blank">Download</a>
              </div>
            </div>
          </div>

          <div class="col-12 info-item">
            <div class="icon bg-danger-soft">
              <i class="bi bi-briefcase"></i>
            </div>
            <div>
              <small class="text-muted">deskripsi perusahaan</small>
              <div class="fw-semibold">{{ $mitra->deskripsi_perusahaan }}</div>
            </div>
          </div>

          

          

          

        </div>
      </div>
    </div>

  </div>

  {{-- KANAN : STATUS --}}
  <div class="col-md-4">

    <div class="card custom-card status-card">
      <div class="card-header custom-card-header">
        <h6 class="mb-0 fw-semibold">Status</h6>
      </div>

      <div class="card-body text-center">
        @if($mitra->status == 1)
          {{-- APPROVED --}}
          <span class="status-pill approved mb-3">
            <i class="bi bi-check-circle-fill"></i> Disetujui
          </span>

          <p class="text-muted small mb-4">
            Mitra telah disetujui dan dapat mengakses sistem
          </p>

          <div class="active-box">
            <i class="bi bi-check-circle-fill"></i>
            <div class="fw-semibold mt-2">Mitra Aktif</div>
          </div>

        @elseif($mitra->status == 0)
          {{-- PENDING --}}
          <span class="status-pill pending mb-3">
            <i class="bi bi-clock-history"></i> Pending
          </span>

          <p class="text-muted small mb-4">
            Pengajuan mitra sedang menunggu proses verifikasi
          </p>

          <div class="active-box pending-box">
            <i class="bi bi-clock-history"></i>
            <div class="fw-semibold mt-2">Menunggu Persetujuan</div>
          </div>

        @else
          {{-- REJECTED --}}
          <span class="status-pill rejected mb-3">
            <i class="bi bi-x-circle-fill"></i> Ditolak
          </span>

          <p class="text-muted small mb-4">
            Pengajuan mitra ditolak oleh admin
          </p>

          <div class="active-box rejected-box">
            <i class="bi bi-x-circle-fill"></i>
            <div class="fw-semibold mt-2">Mitra Ditolak</div>
          </div>
        @endif

      </div>

    </div>

  </div>
</div>

  {{-- RIWAYAT APPROVAL --}}
  <div class="col-md-8">
    <div class="card custom-card mt-4">
      <div class="card-header custom-card-header">
        <h6 class="mb-0 fw-semibold">Riwayat Approval</h6>
      </div>

      <div class="card-body">
        <div class="approval-timeline">

          @foreach($histories as $history)
          <div class="timeline-item">
            <div class="timeline-icon {{ $history->action == 'approved' ? 'success' : 'primary' }}">
              <i class="bi {{ $history->action == 'approved' ? 'bi-check' : 'bi-clock' }}"></i>
            </div>

            <div class="timeline-content">
              <div class="d-flex justify-content-between">
                <span class="fw-semibold">
                  {{ ucfirst($history->action) }}
                </span>
                <small class="text-muted">
                  {{ \Carbon\Carbon::parse($history->created_at)->translatedFormat('d F Y, H:i') }}
                </small>
              </div>

              <p class="mb-1 text-muted small">{{ $history->description }}</p>
              <small class="text-muted">Oleh: {{ $history->user->name ?? 'System' }}</small>
            </div>
          </div>
          @endforeach

        </div>
      </div>
    </div>
  </div>


</div>

<style>
  .custom-card {
  border: 1px solid #eef0f2;
  border-radius: 14px;
  box-shadow: 0 2px 8px rgba(0,0,0,.04);
}

.custom-card-header {
  background: #fff;
  border-bottom: 1px solid #eef0f2;
  padding: 14px 18px;
}

.info-item {
  display: flex;
  gap: 14px;
  align-items: center;
}

.icon {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

/* soft color */
.bg-primary-soft { background: #e7f0ff; color: #0d6efd; }
.bg-success-soft { background: #e6f4ea; color: #198754; }
.bg-warning-soft { background: #fff4e5; color: #fd7e14; }
.bg-danger-soft { background: #fdecea; color: #dc3545; }
.bg-info-soft { background: #e8f6f8; color: #0dcaf0; }
.bg-purple-soft { background: #f1e9ff; color: #6f42c1; }
.bg-secondary-soft { background: #f1f3f5; color: #6c757d; }
.bg-warning-soft { background: #fff4e5; color: #fd7e14; }
.bg-green-soft { background: #ECF4E8; color: #005461; }


/* Status */
.status-card {
  height: fit-content;
}

.status-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 500;
}

.status-pill.approved {
  background: #e6f4ea;
  color: #198754;
}

.active-box {
  border: 1px solid #b7ebc6;
  background: #f0fff4;
  border-radius: 12px;
  padding: 18px;
  color: #198754;
  font-size: 22px;
}

/* Timeline */
.approval-timeline {
  position: relative;
  padding-left: 36px;
}

.approval-timeline::before {
  content: '';
  position: absolute;
  left: 15px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #e9ecef;
}

.timeline-item {
  display: flex;
  gap: 14px;
  margin-bottom: 24px;
}

.timeline-icon {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: #e7f0ff;
  color: #0d6efd;
  display: flex;
  align-items: center;
  justify-content: center;
}

.timeline-icon.success {
  background: #e6f4ea;
  color: #198754;
}

.timeline-content {
  background: #f8f9fa;
  padding: 12px 16px;
  border-radius: 10px;
  width: 100%;
}

</style>
@endsection
