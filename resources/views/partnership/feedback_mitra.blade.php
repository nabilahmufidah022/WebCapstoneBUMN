@extends('layout.index')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                {{-- Header Visual --}}
                <div class="card-header bg-primary text-white p-4 text-center border-0">
                    <i class="bx bx-message-square-detail fs-1 mb-2"></i>
                    <h4 class="fw-bold mb-0">Evaluasi Kegiatan</h4>
                    <p class="small opacity-75 mb-0">Masukan Anda sangat berharga bagi pengembangan program kami</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    {{-- Detail Agenda Pelatihan --}}
                    <div class="mb-4 text-center">
                        <span class="badge bg-primary-soft text-primary px-3 rounded-pill mb-2">Materi Pelatihan</span>
                        <h5 class="fw-bold text-dark mb-1">{{ $participation->judul_pelatihan }}</h5>
                        <p class="text-muted small mb-0">
                            <i class="bx bx-calendar me-1"></i> {{ \Carbon\Carbon::parse($participation->tanggal_pelatihan)->translatedFormat('d F Y') }}
                        </p>
                    </div>

                    <hr class="opacity-10 mb-4">

                    <form action="{{ route('mitra.participation.feedback.store', $participation->id) }}" method="POST">
                        @csrf
                        {{-- Input Kepuasan (Star Rating) --}}
                        <div class="mb-5 text-center">
                            <label class="form-label d-block fw-bold text-dark mb-3">Seberapa puas Anda dengan pelaksanaan pelatihan ini?</label>
                            <div class="d-flex justify-content-center gap-2 flex-row-reverse rating-stars">
                                @for($i=5; $i>=1; $i--)
                                <input type="radio" id="star{{ $i }}" name="kepuasan" value="{{ $i }}" class="btn-check" required>
                                <label for="star{{ $i }}" class="star-label" title="{{ $i }} Bintang">
                                    <i class="bx bxs-star fs-1"></i>
                                </label>
                                @endfor
                            </div>
                            <div class="mt-2 text-muted small" id="ratingText">Pilih tingkat kepuasan</div>
                        </div>

                        {{-- Input Saran & Masukan --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Saran & Masukan</label>
                            <textarea name="saran" class="form-control rounded-3 border-light bg-light p-3" 
                                rows="5" placeholder="Berikan saran atau kritik yang membangun untuk Rumah BUMN Jakarta..." required></textarea>
                            <div class="form-text text-muted small">Masukan ini akan membantu kami menyusun kurikulum pelatihan yang lebih baik di masa depan.</div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill py-3 fw-bold shadow-sm">
                                <i class="bx bx-send me-1"></i> Kirim Feedback
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-link text-muted btn-sm">Batal dan Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1.2rem !important; }
    .bg-primary-soft { background-color: #eef2ff; color: #4338ca; }
    
    /* Logic Visual Rating Bintang */
    .rating-stars { display: flex; }
    .star-label { color: #dee2e6; cursor: pointer; transition: color 0.2s ease; }
    .rating-stars input:checked ~ .star-label,
    .rating-stars .star-label:hover,
    .rating-stars .star-label:hover ~ .star-label {
        color: #ffc107;
    }
    
    .form-control:focus {
        background-color: #fff;
        border-color: #4e73df;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.1);
    }
</style>

<script>
    // Script untuk memberikan teks dinamis sesuai jumlah bintang yang dipilih
    document.querySelectorAll('.rating-stars input').forEach(radio => {
        radio.addEventListener('change', (e) => {
            const val = e.target.value;
            const labels = {
                '1': 'Sangat Tidak Puas',
                '2': 'Kurang Puas',
                '3': 'Cukup Puas',
                '4': 'Puas',
                '5': 'Sangat Puas'
            };
            document.getElementById('ratingText').innerHTML = `<strong class="text-primary">${labels[val]}</strong>`;
        });
    });
</script>
@endsection