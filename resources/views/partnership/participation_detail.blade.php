@extends('layout.index')

@section('content')
<div class="container-fluid px-4 mt-4">

    <!-- HEADER -->
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Detail Keikutsertaan Mitra</h4>
        <p class="text-muted mb-0">
            Informasi lengkap keikutsertaan mitra dalam pelatihan
        </p>
    </div>

    <!-- CARD DETAIL -->
    <div class="card detail-card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            <div class="detail-row">
                <span class="detail-label">Nama Mitra</span>
                <span class="detail-value">{{ $participation->mitra->nama_perusahaan }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Judul Pelatihan</span>
                <span class="detail-value">{{ $participation->judul_pelatihan }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Tanggal Pelatihan</span>
                <span class="detail-value">
                    {{ \Carbon\Carbon::parse($participation->tanggal_pelatihan)->translatedFormat('d F Y') }}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Waktu Pelatihan</span>
                <span class="detail-value">{{ $participation->waktu_pelatihan }} WIB</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Tempat Pelatihan</span>
                <span class="detail-value">{{ $participation->tempat_pelatihan }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Narasumber</span>
                <span class="detail-value">{{ $participation->narasumber }}</span>
            </div>

            <div class="detail-row border-0">
                <span class="detail-label">Status Pelatihan</span>
                <span class="detail-value">
                    @if($participation->status === 'online')
                        <span class="badge badge-online">Online</span>
                    @else
                        <span class="badge badge-offline">Offline</span>
                    @endif
                </span>
            </div>

            <!-- FOOTER -->
            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center"
                style="position: relative; z-index: 10;">

                <!-- LEFT ACTION -->
                <div>
                    <a href="{{ route('mitra.participation.index') }}"
                    class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>

                    <a href="{{ route('mitra.participation.feedback', ['id' => $participation->id, 'section' => 'admin']) }}"
                    class="btn btn-success rounded-pill px-4 ms-2">
                        <i class="bi bi-chat-left-text me-1"></i> Feedback
                    </a>

                    <a href="{{ route('mitra.participation.feedback', ['id' => $participation->id, 'section' => 'user']) }}"
                    class="btn btn-outline-primary rounded-pill px-4 ms-2">
                        <i class="bi bi-eye me-1"></i> Lihat Feedback
                    </a>
                </div>

                <!-- RIGHT ACTION (DANGER) -->
                <form action="{{ route('mitra.participation.destroy', $participation->id) }}"
                    method="POST"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus keikutsertaan mitra ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-4">
                        <i class="bi bi-trash me-1"></i> Hapus Keikutsertaan
                    </button>
                </form>

            </div>


        </div>
    </div>

</div>

<style>
    .detail-card {
    max-width: 100%;
    background-color: #ffffff;
}

.detail-row {
    display: grid;
    grid-template-columns: 260px 1fr;
    padding: 14px 0;
    border-bottom: 1px solid #f1f3f5;
}

.detail-label {
    color: #6c757d;
    font-weight: 500;
}

.detail-value {
    color: #212529;
    font-weight: 500;
}

.badge-online {
    background-color: #e6f9ee;
    color: #198754;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 500;
}

.badge-offline {
    background-color: #e7f0ff;
    color: #0d6efd;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 500;
}
</style>
@endsection

