@extends('layout.index')

@section('content')
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="fw-bold mb-0 d-flex align-items-center gap-2">
        <i class="bx bxs-user-detail text-primary"></i> Detail Mitra
      </h3>
      <small class="text-muted">
        Informasi lengkap data mitra dan riwayat approval sistem
      </small>
    </div>

    {{-- HEADER STATUS BADGE --}}
    <div class="d-flex align-items-center gap-3">
        @if($mitra->status == 0)
            <span class="status-pill pending">
                <i class="bi bi-clock-history"></i> Pending
            </span>
        @elseif($mitra->status == 1)
            <span class="status-pill on-review">
                <i class="bi bi-search"></i> On Review
            </span>
        @elseif($mitra->status == 2)
            <span class="status-pill approved">
                <i class="bi bi-check-circle-fill"></i> Disetujui
            </span>
        @else
            <span class="status-pill rejected">
                <i class="bi bi-x-circle-fill"></i> Ditolak
            </span>
        @endif

        <a href="{{ route('kelola_pendaftaran') }}" class="btn btn-light shadow-sm border px-3">
          <i class="bx bx-left-arrow-alt"></i> Kembali
        </a>
    </div>
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
                <small class="text-muted d-block">Nama Perusahaan</small>
                <div class="fw-semibold">{{ $mitra->nama_perusahaan }}</div>
              </div>
            </div>

            {{-- FITUR PENANGANAN REALTIME ARRAY TO BADGES (REVISI DOSEN) --}}
            <div class="col-md-6 info-item">
              <div class="icon bg-info-soft">
                <i class="bi bi-tags"></i>
              </div>
              <div>
                <small class="text-muted d-block">Bidang Perusahaan / Kategori</small>
                <div class="fw-semibold mt-1">
                  @if(is_array($mitra->bidang_perusahaan) && count($mitra->bidang_perusahaan) > 0)
                    <div class="d-flex flex-wrap gap-1">
                      @foreach($mitra->bidang_perusahaan as $bidang)
                        <span class="badge bg-light text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill small fw-bold" style="font-size: 12px;">
                          {{ $bidang }}
                        </span>
                      @endforeach
                    </div>
                  @else
                    <span class="badge bg-light text-secondary px-2 py-1 rounded-pill small fw-bold" style="font-size: 12px;">
                      {{ $mitra->bidang_perusahaan ?? '-' }}
                    </span>
                  @endif
                </div>
              </div>
            </div>

            <div class="col-md-6 info-item">
              <div class="icon bg-purple-soft">
                <i class="bi bi-person"></i>
              </div>
              <div>
                <small class="text-muted d-block">Nama PIC</small>
                <div class="fw-semibold">{{ $mitra->nama_lengkap }}</div>
              </div>
            </div>

            <div class="col-md-6 info-item">
              <div class="icon bg-success-soft">
                <i class="bi bi-envelope"></i>
              </div>
              <div>
                <small class="text-muted d-block">Email PIC</small>
                <div class="fw-semibold">{{ $mitra->user->email ?? '-' }}</div>
              </div>
            </div>

            <div class="col-md-6 info-item">
              <div class="icon bg-warning-soft">
                <i class="bi bi-telephone"></i>
              </div>
              <div>
                <small class="text-muted d-block">No. Telepon PIC</small>
                <div class="fw-semibold">{{ $mitra->no_telepon }}</div>
              </div>
            </div>

            <div class="col-md-6 info-item">
              <div class="icon bg-secondary-soft">
                <i class="bi bi-geo-alt"></i>
              </div>
              <div>
                <small class="text-muted d-block">Alamat</small>
                <div class="fw-semibold">{{ $mitra->lokasi_perusahaan }}</div>
              </div>
            </div>

            <div class="col-md-6 info-item">
              <div class="icon bg-info-soft">
                <i class="bi bi-calendar-event"></i>
              </div>
              <div>
                <small class="text-muted d-block">Tanggal Pengajuan</small>
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
                <small class="text-muted d-block">Company Profile</small>
                <div class="fw-semibold">
                  @if($mitra->company_profile)
                    <a href="{{ asset('uploads/mitra/' . $mitra->company_profile) }}" target="_blank">Download</a>
                  @else
                    <span class="text-muted small">-</span>
                  @endif
                </div>
              </div>
            </div>

            <div class="col-md-6 info-item">
              <div class="icon bg-green-soft">
                <i class="bi bi-file-earmark-text-fill"></i>
              </div>
              <div>
                <small class="text-muted d-block">Surat Permohonan Audiensi:</small>
                <div class="fw-semibold">
                  @if($mitra->surat_permohonan_audiensi)
                    <a href="{{ asset('uploads/mitra/' . $mitra->surat_permohonan_audiensi) }}" target="_blank">Download</a>
                  @else
                    <span class="text-muted small">-</span>
                  @endif
                </div>
              </div>
            </div>

            <div class="col-12 info-item border-top pt-3">
              <div class="icon bg-danger-soft">
                <i class="bi bi-briefcase"></i>
              </div>
              <div>
                <small class="text-muted d-block">Deskripsi Perusahaan</small>
                <div class="fw-semibold">{{ $mitra->deskripsi_perusahaan }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- RIWAYAT APPROVAL --}}
      <div class="card custom-card">
        <div class="card-header custom-card-header">
          <h6 class="mb-0 fw-semibold">Riwayat Approval</h6>
        </div>
        <div class="card-body">
          <div class="approval-timeline">
            @foreach($histories as $history)
            <div class="timeline-item">
              <div class="timeline-icon {{ in_array($history->action, ['approved', 'accepted']) ? 'success' : 'primary' }}">
                <i class="bi {{ in_array($history->action, ['approved', 'accepted']) ? 'bi-check' : 'bi-clock' }}"></i>
              </div>
              <div class="timeline-content">
                <div class="d-flex justify-content-between">
                  <span class="fw-semibold">{{ ucfirst($history->action) }}</span>
                  <small class="text-muted">{{ \Carbon\Carbon::parse($history->created_at)->translatedFormat('d F Y, H:i') }}</small>
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

    {{-- KANAN : STATUS & ACTION BOX --}}
    <div class="col-md-4">

      <div class="card custom-card status-card mb-4">
        <div class="card-header custom-card-header text-center">
          <h6 class="mb-0 fw-semibold">Status Saat Ini</h6>
        </div>
        <div class="card-body text-center">
          @if($mitra->status == 2)
            <div class="active-box">
              <i class="bi bi-check-circle-fill"></i>
              <div class="fw-semibold mt-2">Mitra Disetujui</div>
            </div>
          @elseif($mitra->status == 1)
            <div class="active-box on-review-box">
              <i class="bi bi-search"></i>
              <div class="fw-semibold mt-2">Sedang Diperiksa</div>
            </div>
          @elseif($mitra->status == 0)
            <div class="active-box pending-box">
              <i class="bi bi-clock-history"></i>
              <div class="fw-semibold mt-2">Menunggu Persetujuan</div>
            </div>
          @else
            <div class="active-box rejected-box">
              <i class="bi bi-x-circle-fill"></i>
              <div class="fw-semibold mt-2">Mitra Ditolak</div>
            </div>
          @endif
        </div>
      </div>

      {{-- TINDAKAN ADMIN --}}
      @if(Auth::user()->usertype == 'admin')
      <div class="card custom-card">
          <div class="card-header custom-card-header bg-light">
              <h6 class="mb-0 fw-semibold">Tindakan Admin</h6>
          </div>
          <div class="card-body">
              <div class="d-grid gap-2">
                  
                  {{-- ==========================================================================
                       KONDISI A: JIKA STATUSNYA BARU DAFTAR / PENDING (STATUS = 0)
                       ========================================================================== --}}
                  @if($mitra->status == 0)
                      <div class="alert alert-info small mb-2 text-start" style="border-radius: 8px; line-height: 1.4;">
                          <i class="bx bx-info-circle me-1"></i> Harap klik tombol <strong>Mulai Periksa</strong> terlebih dahulu untuk membuka akses verifikasi.
                      </div>

                      <form action="{{ route('mitra.review', $mitra->id) }}" method="POST" class="m-0">
                          @csrf
                          <button type="submit" class="btn btn-info w-100 fw-bold text-dark rounded-pill shadow-sm py-2">
                              <i class="bx bx-search-alt"></i> Mulai Periksa (Review)
                          </button>
                      </form>

                      {{-- Tombol Setujui & Tolak Dikunci (Disabled) Secara Interaktif --}}
                      <button type="button" class="btn btn-success fw-bold rounded-pill opacity-50 py-2" disabled style="cursor: not-allowed;">
                          <i class="bx bx-check"></i> Setujui Mitra (Terkunci)
                      </button>
                      <button type="button" class="btn btn-danger fw-bold rounded-pill opacity-50 py-2" disabled style="cursor: not-allowed;">
                          <i class="bx bx-x"></i> Tolak Pendaftaran (Terkunci)
                      </button>

                  {{-- ==========================================================================
                       KONDISI B: JIKA STATUSNYA SUDAH SEDANG DITINJAU / ON REVIEW (STATUS = 1)
                       ========================================================================== --}}
                  @elseif($mitra->status == 1)
                      <form action="{{ route('mitra.approve', $mitra->id) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin MENYETUJUI kemitraan ini?')">
                          @csrf
                          <button type="submit" class="btn btn-success w-100 fw-bold rounded-pill shadow-sm py-2">
                              <i class="bx bx-check"></i> Setujui Mitra
                          </button>
                      </form>

                      <form action="{{ route('mitra.reject', $mitra->id) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK pendaftaran kemitraan ini?')">
                          @csrf
                          <button type="submit" class="btn btn-danger w-100 fw-bold rounded-pill shadow-sm py-2">
                              <i class="bx bx-x"></i> Tolak Pendaftaran
                          </button>
                      </form>

                  {{-- ==========================================================================
                       KONDISI C: JIKA SUDAH SELESAI DIPROSES FINAL (STATUS = 2 atau 3)
                       ========================================================================== --}}
                  @else
                      <div class="alert alert-light border text-center mb-0" style="border-radius: 8px;">
                          <p class="small text-muted mb-0"><i class="bx bx-lock-alt me-1"></i> Pendaftaran ini sudah diproses dan tidak memerlukan tindakan admin lagi.</p>
                      </div>
                  @endif

              </div>
          </div>
      </div>
      @endif
    </div>
  </div>
</div>

<style>
  .custom-card { border: 1px solid #eef0f2; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,.04); }
  .custom-card-header { background: #fff; border-bottom: 1px solid #eef0f2; padding: 14px 18px; }
  .info-item { display: flex; gap: 14px; align-items: center; }
  .icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }

  /* soft color */
  .bg-primary-soft { background: #e7f0ff; color: #0d6efd; }
  .bg-success-soft { background: #e6f4ea; color: #198754; }
  .bg-warning-soft { background: #fff4e5; color: #fd7e14; }
  .bg-danger-soft { background: #fdecea; color: #dc3545; }
  .bg-info-soft { background: #e8f6f8; color: #0dcaf0; }
  .bg-purple-soft { background: #f1e9ff; color: #6f42c1; }
  .bg-secondary-soft { background: #f1f3f5; color: #6c757d; }
  .bg-green-soft { background: #ECF4E8; color: #005461; }

  /* Status Pills */
  .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 500; }
  .status-pill.approved { background: #e6f4ea; color: #198754; }
  .status-pill.pending { background: #fff4e5; color: #fd7e14; }
  .status-pill.on-review { background: #e0f2ff; color: #007bff; }
  .status-pill.rejected { background: #fdecea; color: #dc3545; }

  .active-box { border: 1px solid #b7ebc6; background: #f0fff4; border-radius: 12px; padding: 18px; color: #198754; font-size: 22px; }
  .active-box.pending-box { border: 1px solid #ffeeba; background: #fff3cd; color: #856404; }
  .active-box.on-review-box { border: 1px solid #b8daff; background: #e7f3ff; color: #004085; }
  .active-box.rejected-box { border: 1px solid #f5c6cb; background: #f8d7da; color: #721c24; }

  /* Timeline */
  .approval-timeline { position: relative; padding-left: 36px; }
  .approval-timeline::before { content: ''; position: absolute; left: 15px; top: 0; bottom: 0; width: 2px; background: #e9ecef; }
  .timeline-item { display: flex; gap: 14px; margin-bottom: 24px; }
  .timeline-icon { width: 30px; height: 30px; border-radius: 50%; background: #e7f0ff; color: #0d6efd; display: flex; align-items: center; justify-content: center; }
  .timeline-icon.success { background: #e6f4ea; color: #198754; }
  .timeline-content { background: #f8f9fa; padding: 12px 16px; border-radius: 10px; width: 100%; }
</style>
@endsection