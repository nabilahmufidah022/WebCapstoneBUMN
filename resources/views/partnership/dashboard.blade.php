@extends('layout.index')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1" style="letter-spacing: -0.02em;">Dashboard Monitoring</h3>

            {{-- LOGIKA SINKRONISASI UX: MEMBEDAKAN SAPAAN BERDASARKAN STATUS LEGALITAS DI DATABASE --}}
            <p class="text-muted mb-0">
                @if(Auth::user()->usertype == 'admin' || (Auth::user()->mitra && Auth::user()->mitra->status == 2))
                    {{-- Kondisi 1: Untuk Admin atau Mitra lama yang SUDAH DI-APPROVE (Status 2) --}}
                    Selamat datang kembali, <strong>{{ Auth::user()->name }}</strong> 👋 Berikut ringkasan aktivitas sistem hari ini.
                @else
                    {{-- Kondisi 2: Untuk Akun Baru Register, atau yang BARU SUBMIT FORM (Status masih pending/0/1) --}}
                    Selamat datang, <strong>{{ Auth::user()->name }}</strong> 🎉 Berikut ringkasan aktivitas sistem Anda hari ini.
                @endif
            </p>
        </div>
        <div class="text-end">
            <span class="badge bg-white shadow-sm text-dark p-2 px-3 rounded-pill">
                <i class="bx bx-calendar me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    {{-- ==========================================================================
         DASHBOARD ADMIN (SERVER ADMIN)
         ========================================================================== --}}
    @if(Auth::user()->usertype == 'admin')

        {{-- BARIS GRAFIK MONITORING --}}
        <div class="row g-4 mb-4">
            {{-- GRAFIK 1: TOTAL MITRA (PIE CHART) --}}
            <div class="col-md-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 py-3 text-center">
                        <h6 class="text-muted small fw-bold text-uppercase mb-0">Total Mitra Keseluruhan</h6>
                    </div>
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                        <div style="height: 220px; width: 100%;">
                            <canvas id="totalMitraPieChart"></canvas>
                        </div>
                        <div class="text-center mt-3">
                            <h2 class="fw-bold mb-0 text-primary">{{ $totalMitra }}</h2>
                            <small class="text-muted">Mitra Terdaftar</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- GRAFIK 2: AGENDA PER BULAN (BAR CHART) --}}
            <div class="col-md-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-muted small fw-bold text-uppercase mb-0">Agenda Pelatihan Per Bulan</h6>
                        <span class="badge bg-success text-white rounded-pill px-3">Total: {{ $totalParticipations }}</span>
                    </div>
                    <div class="card-body">
                        <div style="height: 280px; width: 100%;">
                            <canvas id="agendaMonthlyBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL AKTIVITAS TERBARU ADMIN --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="bx bx-list-ul me-2 text-primary"></i>Aktivitas Silabus Terbaru</h5>
                <a href="{{ route('mitra.participation.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="bg-light text-muted small fw-bold">
                            <tr>
                                <th class="ps-4">NAMA MITRA</th>
                                <th>JUDUL MATERI</th>
                                <th>TANGGAL</th>
                                <th class="pe-4 text-center">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentParticipations as $participation)
                            <tr>
                                <td class="ps-4 fw-semibold text-primary">
                                    {{ $participation->mitra->nama_perusahaan ?? 'Admin Internal' }}
                                </td>
                                <td>{{ $participation->judul_pelatihan }}</td>
                                <td>
                                    <span class="text-muted small">
                                        {{ \Carbon\Carbon::parse($participation->tanggal_pelatihan)->translatedFormat('d M Y') }}
                                    </span>
                                </td>
                                <td class="pe-4 text-center">
                                    @if($participation->status == 'Selesai')
                                        <span class="badge bg-success px-3 rounded-pill fw-bold">SELESAI</span>
                                    @else
                                        <span class="badge bg-primary px-3 rounded-pill fw-bold">AKAN DATANG</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <span class="small">Belum ada agenda pelatihan yang tercatat.</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    {{-- ==========================================================================
         DASHBOARD MITRA (SERVER MITRA)
         ========================================================================== --}}
    @else
        @if($mitra && $mitra->status == 2)
            {{-- TAMPILAN MITRA TERVERIFIKASI --}}
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="small fw-bold text-uppercase opacity-75">Total Kerjasama</h6>
                                    <h2 class="fw-bold mb-0">{{ $data['total_kerjasama'] }}</h2>
                                </div>
                                <i class="bx bx-briefcase fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-success text-white">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="small fw-bold text-uppercase opacity-75">Agenda Mendatang</h6>
                                    <h2 class="fw-bold mb-0">{{ $data['agenda_mendatang'] }}</h2>
                                </div>
                                <i class="bx bx-calendar-event fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 {{ $data['perlu_evaluasi'] > 0 ? 'bg-warning text-dark' : 'bg-light text-muted' }}">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="small fw-bold text-uppercase opacity-75">Perlu Evaluasi</h6>
                                    <h2 class="fw-bold mb-0">{{ $data['perlu_evaluasi'] }}</h2>
                                </div>
                                <i class="bx bx-star fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- HISTORI TERBARU MITRA --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Agenda & Pelatihan Terakhir</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light text-muted small fw-bold">
                                <tr>
                                    <th class="ps-4">MATERI PELATIHAN</th>
                                    <th>TANGGAL</th>
                                    <th>STATUS</th>
                                    {{-- Mengubah text-end menjadi text-center --}}
                                    <th class="pe-4 text-center" style="width: 180px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($participations as $p)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $p->judul_pelatihan }}</div>
                                        <small class="text-muted">{{ $p->kategori }}</small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($p->tanggal_pelatihan)->translatedFormat('d M Y') }}</td>
                                    <td>
                                        @if($p->status == 'Selesai')
                                            <span class="badge bg-success-soft text-success rounded-pill px-3">Selesai</span>
                                        @else
                                            <span class="badge bg-primary-soft text-primary rounded-pill px-3">Terjadwal</span>
                                        @endif
                                    </td>
                                    {{-- Mengubah text-end menjadi text-center serta menyelaraskan flex layout --}}
                                    <td class="pe-4 text-center">

                                        {{-- LOGIKA AKSI --}}
                                        @if($p->status == 'Selesai')

                                            @if(!isset($p->rating_mitra) || is_null($p->rating_mitra))
                                                {{-- Tombol Beri Feedback --}}
                                                <a href="{{ route('mitra.participation.feedback', $p->id) }}"
                                                   class="btn btn-sm btn-warning rounded-pill fw-bold shadow-sm px-3"
                                                   style="font-size: 10px; min-width: 110px;">
                                                     Berikan Feedback
                                                </a>
                                            @else
                                                {{-- Area Feedback Terkirim & Detail --}}
                                                <div class="d-flex flex-column align-items-center gap-1">

                                                    <span class="badge bg-success-soft text-success rounded-pill px-2 py-1"
                                                          style="font-size: 10px; min-width: 110px; border: 1px solid rgba(30, 126, 52, 0.2);">
                                                        <i class="bx bx-check-circle"></i> Feedback Terkirim
                                                    </span>

                                                    <a href="{{ route('mitra.participation.show', $p->id) }}"
                                                       class="btn btn-sm btn-outline-primary rounded-pill py-0 px-2 fw-bold"
                                                       style="font-size: 10px; min-width: 110px; line-height: 1.8;">
                                                         Detail
                                                    </a>

                                                </div>
                                            @endif

                                        @else
                                            {{-- Tombol Detail untuk status Terjadwal --}}
                                            <a href="{{ route('mitra.participation.show', $p->id) }}"
                                               class="btn btn-sm btn-light border rounded-pill fw-bold px-3 shadow-sm"
                                               style="font-size: 10px; min-width: 110px;">
                                                 Detail
                                            </a>
                                        @endif

                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bx bx-info-circle fs-4 d-block mb-2"></i>
                                        <span class="small">Belum ada riwayat kegiatan pelatihan.</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            {{-- TRACKING STATUS PENDAFTARAN --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5 text-center">
                    @if(!$mitra)
                        <div class="mb-4">
                            <i class="bx bx-rocket text-primary" style="font-size: 5rem;"></i>
                        </div>
                        <h4 class="fw-bold">Mulai Kerjasama Anda!</h4>
                        <p class="text-muted mx-auto" style="max-width: 500px;">
                            Anda belum terdaftar sebagai mitra resmi Rumah BUMN Jakarta.
                            Silakan lengkapi formulir pendaftaran untuk mulai berkolaborasi.
                        </p>
                        <a href="{{ route('daftar_mitra') }}" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow">
                            Daftar Menjadi Mitra
                        </a>
                    @else
                        <div class="mb-4">
                            <i class="bx bx-time-five text-warning" style="font-size: 5rem;"></i>
                        </div>
                        <h4 class="fw-bold">Pendaftaran Sedang Diproses</h4>
                        <p class="text-muted mb-4">
                            Tim kami sedang meninjau dokumen pendaftaran Anda.
                            Mohon tunggu informasi selanjutnya melalui email atau dashboard ini.
                        </p>

                        <div class="d-flex justify-content-center align-items-center gap-3 mb-2">
                            <div class="text-center">
                                <div class="badge bg-success rounded-circle p-2 mb-2">
                                    <i class="bx bx-check"></i>
                                </div>
                                <div class="small fw-bold">Daftar</div>
                            </div>

                            <div style="width: 50px; height: 2px; background: #dee2e6;"></div>

                            <div class="text-center">
                                <div class="badge {{ $mitra->status == 1 ? 'bg-primary' : 'bg-secondary' }} rounded-circle p-2 mb-2">
                                    <i class="bx bx-search"></i>
                                </div>
                                <div class="small fw-bold">Ditinjau</div>
                            </div>

                            <div style="width: 50px; height: 2px; background: #dee2e6;"></div>

                            <div class="text-center">
                                <div class="badge bg-secondary rounded-circle p-2 mb-2">
                                    <i class="bx bx-buildings"></i>
                                </div>
                                <div class="small fw-bold">Aktif</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>

{{-- ==========================================================================
     SCRIPTS AREA
     ========================================================================== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(Auth::user()->usertype == 'admin')

            // Pie Chart Total Mitra
            const ctxPie = document.getElementById('totalMitraPieChart').getContext('2d');
            new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($mitraPieData['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($mitraPieData['data']) !!},
                        backgroundColor: ['#4e73df'],
                        hoverBackgroundColor: ['#2e59d9'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Bar Chart Agenda Bulanan
            const ctxBar = document.getElementById('agendaMonthlyBarChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($agendaTrend->pluck('month')) !!},
                    datasets: [{
                        label: "Jumlah Agenda",
                        data: {!! json_encode($agendaTrend->pluck('total')) !!},
                        backgroundColor: '#1cc88a',
                        hoverBackgroundColor: '#17a673',
                        borderRadius: 5,
                        barThickness: 30
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { color: '#f8f9fa' }
                        }
                    }
                }
            });

        @endif
    });
</script>

{{-- ==========================================================================
     STYLING AREA
     ========================================================================== --}}
<style>
    /* Global Card styling */
    .rounded-4 { border-radius: 1rem !important; }

    /* Table styling */
    .table th { border-bottom: none !important; }

    /* Badge Soft styling */
    .bg-success-soft {
        background-color: #e6f4ea !important;
        color: #1e7e34 !important;
    }

    .bg-primary-soft {
        background-color: #cfe2ff;
        color: #084298;
    }

    /* Buttons Custom styling */
    .btn-outline-primary {
        border-color: #4e73df;
        color: #4e73df;
    }

    .btn-outline-primary:hover {
        background-color: #4e73df;
        color: white;
    }

    /* Vertical alignment for icons in badges */
    .badge i {
        vertical-align: middle;
        margin-top: -2px;
    }

    /* Additional spacing for action column */
    .table td {
        padding-top: 12px;
        padding-bottom: 12px;
    }
</style>

@endsection
