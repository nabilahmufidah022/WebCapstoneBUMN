@extends('layout.index')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Kelola Pendaftaran Mitra</h4>
        <span class="badge bg-primary">{{ $mitras->count() }} Pengajuan Baru</span>
    </div>

    @if(session('success')) 
    <div class="alert alert-success alert-dismissible fade show" role="alert"> 
        {{ session('success') }} 
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div> 
    @endif 

    <div class="row">
        @forelse($mitras as $mitra)
        <div class="col-12 mb-3">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-2">
                                <h5 class="fw-bold mb-0 me-2">{{ $mitra->nama_perusahaan }}</h5>
                                <span class="badge bg-warning text-dark">Pending</span>
                            </div>
                            <div class="text-muted small mb-3">
                                <i class="bx bx-user"></i> PIC: {{ $mitra->nama_lengkap }} &nbsp;&nbsp;
                                <i class="bx bx-phone"></i> {{ $mitra->no_telepon }} &nbsp;&nbsp;
                                <i class="bx bx-map"></i> {{ $mitra->lokasi_perusahaan }}
                            </div>
                            <p class="mb-0 text-secondary" style="font-size: 14px;">
                                {{ Str::limit($mitra->deskripsi_perusahaan, 150) }}
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="d-flex justify-content-md-end gap-2">
                                <a href="{{ route('mitra.detail', $mitra->id) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bx bx-show"></i> Detail
                                </a>

                                <form action="{{ route('mitra.approve', $mitra->id) }}" method="POST" onsubmit="return confirm('Setujui pendaftaran mitra ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bx bx-check"></i> Setujui
                                    </button>
                                </form>

                                <form action="{{ route('mitra.reject', $mitra->id) }}" method="POST" onsubmit="return confirm('Tolak pendaftaran mitra ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bx bx-x"></i> Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5 bg-white shadow-sm" style="border-radius: 12px;">
                <i class="bx bx-envelope-open fs-1 text-muted"></i>
                <p class="mt-3 text-muted">Tidak ada pengajuan pendaftaran mitra baru saat ini.</p>
                <a href="{{ route('list_mitra') }}" class="btn btn-primary btn-sm">Lihat Mitra Aktif</a>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection