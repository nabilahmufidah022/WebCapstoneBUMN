@extends('layout.index')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 text-center">
                    <h5 class="fw-bold mb-0">Penilaian Agenda Pelatihan</h5>
                </div>
                <div class="card-body p-4 text-center">
                    <h6 class="text-primary fw-bold">{{ $participation->judul_pelatihan }}</h6>
                    <p class="text-muted small">Mitra: {{ $participation->mitra->nama_perusahaan }}</p>
                    <hr>
                    
                    <form action="{{ route('mitra.participation.rate', $participation->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="d-block small fw-bold text-muted mb-2">BERIKAN RATING (1-5)</label>
                            <input type="number" name="rating" min="1" max="5" class="form-control text-center mx-auto w-25 fs-4 fw-bold" placeholder="5" required>
                            <div class="text-warning mt-2">
                                <i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i>
                            </div>
                        </div>

                        <div class="mb-4 text-start">
                            <label class="small fw-bold text-muted mb-2">CATATAN INTERNAL ADMIN</label>
                            <textarea name="catatan_internal" class="form-control rounded-3" rows="4" placeholder="Masukkan evaluasi atau catatan keterlibatan mitra..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-bold">
                            Selesaikan & Simpan Penilaian
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection