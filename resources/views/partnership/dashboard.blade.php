@extends('layout.index')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- HEADER -->
    <div class="mb-4">
        <h3 class="fw-bold mb-1">Dashboard</h3>
        <p class="text-muted mb-0">Ringkasan data dan aktivitas sistem</p>
    </div>

    {{-- ================= ADMIN DASHBOARD ================= --}}
    @if(Auth::user()->usertype == 'admin')

        <!-- STATISTIC CARDS -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <p class="text-muted mb-1">Total Mitra</p>
                        <h3 class="fw-bold">{{ $totalMitra }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <p class="text-muted mb-1">Pending Mitra</p>
                        <h3 class="fw-bold text-warning">{{ $pendingMitra }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <p class="text-muted mb-1">Approved Mitra</p>
                        <h3 class="fw-bold text-success">{{ $approvedMitra }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <p class="text-muted mb-1">Total Partisipasi</p>
                        <h3 class="fw-bold text-primary">{{ $totalParticipations }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- RECENT PARTICIPATION -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0">
                <h5 class="fw-bold mb-0">Partisipasi Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="text-muted">
                            <tr>
                                <th>Nama Mitra</th>
                                <th>Judul Pelatihan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentParticipations as $participation)
                            <tr>
                                <td>{{ $participation->mitra->nama_perusahaan ?? '-' }}</td>
                                <td>{{ $participation->judul_pelatihan }}</td>
                                <td>{{ \Carbon\Carbon::parse($participation->tanggal_pelatihan)->format('d M Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $participation->status == 'approved' ? 'success' : 
                                        ($participation->status == 'pending' ? 'warning' : 'secondary') 
                                    }}">
                                        {{ ucfirst($participation->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada data partisipasi
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    {{-- ================= USER DASHBOARD ================= --}}
@else

<div class="row g-4">

    <!-- STATUS PENDAFTARAN MITRA -->
    <div class="col-md-7">
    <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-body p-4">

            <!-- TITLE -->
            <h5 class="fw-bold mb-4">Status Pendaftaran Mitra</h5>

            <!-- GRID INFO -->
            <div class="row g-4">

                <div class="col-md-6">
                    <p class="text-muted small mb-1">Nama PIC</p>
                    <p class="fw-semibold mb-0">{{ $mitra->nama_lengkap }}</p>
                </div>

                <div class="col-md-6">
                    <p class="text-muted small mb-1">Nama Perusahaan</p>
                    <p class="fw-semibold mb-0">{{ $mitra->nama_perusahaan }}</p>
                </div>

                <div class="col-md-6">
                    <p class="text-muted small mb-1">Lokasi Perusahaan</p>
                    <p class="fw-semibold mb-0">{{ $mitra->lokasi_perusahaan }}</p>
                </div>

                <div class="col-md-6">
                    <p class="text-muted small mb-1">Status Pendaftaran</p>

                    @if($mitra->status == 0)
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                            Menunggu Verifikasi
                        </span>
                    @elseif($mitra->status == 1)
                        <span class="badge bg-success px-3 py-2 rounded-pill">
                            Disetujui
                        </span>
                    @else
                        <span class="badge bg-danger px-3 py-2 rounded-pill">
                            Ditolak
                        </span>
                    @endif
                </div>

            </div>

            @if($mitra->deskripsi_perusahaan)
                <hr class="my-4">
                <p class="text-muted mb-0">
                    {{ $mitra->deskripsi_perusahaan }}
                </p>
            @endif

        </div>
    </div>
</div>


    <!-- RIWAYAT PARTISIPASI EVENT -->
    <div class="col-md-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0">
                <h5 class="fw-bold mb-0">Riwayat Partisipasi Event</h5>
            </div>
            <div class="card-body">
                @if($participations->count())
                    <ul class="list-group list-group-flush">
                        @foreach($participations as $participation)
                        <li class="list-group-item px-0">
                            <strong>{{ $participation->judul_pelatihan }}</strong><br>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($participation->tanggal_pelatihan)->format('d M Y') }}
                            </small>

                            <span class="badge bg-{{ 
                                $participation->status == 'approved' ? 'success' : 
                                ($participation->status == 'pending' ? 'warning' : 'secondary') 
                            }} float-end">
                                {{ ucfirst($participation->status) }}
                            </span>
                        </li>
                        @endforeach
                    </ul>

                    <a href="{{ route('mitra.participation.index') }}"
                       class="btn btn-outline-primary btn-sm w-100 mt-3">
                        Lihat Detail Partisipasi
                    </a>
                @else
                    <p class="text-muted mb-2">
                        Anda belum mengikuti pelatihan.
                    </p>
                    <a href="{{ route('mitra.participation.create') }}"
                       class="btn btn-success btn-sm w-100">
                        Daftar Pelatihan
                    </a>
                @endif
            </div>
        </div>
    </div>

</div>

@endif


</div>
@endsection
