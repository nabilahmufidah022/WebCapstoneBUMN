@extends('layout.index')

@section('content')

<div class="container py-4">

  @if(session('success'))
  <div class="alert alert-success">
    {{ session('success') }}
  </div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Detail Mitra</h2>
    <a href="{{ route('list_mitra') }}" class="btn btn-secondary">Kembali ke List</a>
  </div>

  <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Informasi Mitra</h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <p><strong>Nama Lengkap:</strong> {{ $mitra->nama_lengkap }}</p>
          <p><strong>No. Telepon:</strong> {{ $mitra->no_telepon }}</p>
          <p><strong>Nama Perusahaan:</strong> {{ $mitra->nama_perusahaan }}</p>
          <p><strong>Lokasi Perusahaan:</strong> {{ $mitra->lokasi_perusahaan }}</p>
        </div>
        <div class="col-md-6">
          <p><strong>Email:</strong> {{ $mitra->user->email }}</p>
          <p><strong>Status:</strong>
            @if($mitra->status == 0)
              <span class="badge bg-warning">Pending</span>
            @elseif($mitra->status == 1)
              <span class="badge bg-success">Disetujui</span>
            @elseif($mitra->status == 2)
              <span class="badge bg-danger">Ditolak</span>
            @endif
          </p>
          <p><strong>Didaftarkan pada:</strong> {{ \Carbon\Carbon::parse($mitra->created_at)->translatedFormat('d F Y H:i') }}</p>
        </div>
      </div>
      <div class="row mt-3">
        <div class="col-12">
          <p><strong>Deskripsi Perusahaan:</strong></p>
          <p>{{ $mitra->deskripsi_perusahaan }}</p>
        </div>
      </div>
      @if($mitra->company_profile)
      <div class="row mt-3">
        <div class="col-12">
          <p><strong>Company Profile:</strong> <a href="{{ asset('uploads/mitra/' . $mitra->company_profile) }}" target="_blank">Download</a></p>
        </div>
      </div>
      @endif
      @if($mitra->surat_permohonan_audiensi)
      <div class="row mt-3">
        <div class="col-12">
          <p><strong>Surat Permohonan Audiensi:</strong> <a href="{{ asset('uploads/mitra/' . $mitra->surat_permohonan_audiensi) }}" target="_blank">Download</a></p>
        </div>
      </div>
      @endif
    </div>
  </div>

  <div class="card shadow-sm border-0" style="border-radius: 12px;">
    <div class="card-header bg-info text-white">
      <h5 class="mb-0">Riwayat Aktivitas</h5>
    </div>
    <div class="card-body">
      @if($histories->isEmpty())
        <p class="text-muted">Belum ada riwayat aktivitas.</p>
      @else
        <div class="timeline">
          @foreach($histories as $history)
          <div class="timeline-item mb-3">
            <div class="timeline-marker bg-primary"></div>
            <div class="timeline-content">
              <h6 class="mb-1">
                @if($history->action == 'registered')
                  <i class="bi bi-person-plus"></i> Pendaftaran Mitra
                @elseif($history->action == 'approved')
                  <i class="bi bi-check-circle"></i> Disetujui
                @elseif($history->action == 'rejected')
                  <i class="bi bi-x-circle"></i> Ditolak
                @endif
              </h6>
              <p class="mb-1">{{ $history->description }}</p>
              <small class="text-muted">
                Oleh: {{ $history->user ? $history->user->name : 'System' }} |
                {{ \Carbon\Carbon::parse($history->created_at)->translatedFormat('d F Y H:i') }}
              </small>
            </div>
          </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>

</div>

<style>
.timeline {
  position: relative;
  padding-left: 30px;
}

.timeline::before {
  content: '';
  position: absolute;
  left: 15px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #e9ecef;
}

.timeline-item {
  position: relative;
  margin-bottom: 20px;
}

.timeline-marker {
  position: absolute;
  left: -22px;
  top: 5px;
  width: 12px;
  height: 12px;
  border-radius: 50%;
}

.timeline-content {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
  border-left: 4px solid #007bff;
}
</style>

@endsection
