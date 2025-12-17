@extends('layout.index')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">Feedback Keikutsertaan Mitra</h4>
            <p class="text-muted mb-0">Berikan atau lihat feedback untuk pelatihan mitra.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 p-4">
        <h5 class="mb-3">{{ $participation->judul_pelatihan }}</h5>
        <p><strong>Mitra:</strong> {{ $participation->mitra->nama_perusahaan }}</p>
        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($participation->tanggal_pelatihan)->translatedFormat('d F Y') }}</p>

        <div class="mt-4">
            <form id="feedbackForm">
                <div class="mb-3">
                    <label for="tujuan_manfaat" class="form-label">Tujuan dan Manfaat Acara</label>
                    <textarea id="tujuan_manfaat" name="tujuan_manfaat" rows="3" class="form-control" placeholder="Jelaskan tujuan dan manfaat acara..." required></textarea>
                    <div class="invalid-feedback">Pertanyaan ini wajib diisi</div>
                </div>

                <div class="mb-3">
                    <label for="materi_narasumber" class="form-label">Materi dan Narasumber</label>
                    <textarea id="materi_narasumber" name="materi_narasumber" rows="3" class="form-control" placeholder="Tuliskan materi dan penilaian narasumber..." required></textarea>
                    <div class="invalid-feedback">Pertanyaan ini wajib diisi</div>
                </div>

                <div class="mb-3">
                    <label for="susunan_waktu" class="form-label">Susunan Acara dan Waktu</label>
                    <textarea id="susunan_waktu" name="susunan_waktu" rows="3" class="form-control" placeholder="Komentar mengenai susunan acara dan waktu..." required></textarea>
                    <div class="invalid-feedback">Pertanyaan ini wajib diisi</div>
                </div>

                <div class="mb-3">
                    <label for="teknis_fasilitas" class="form-label">Teknis dan Fasilitas</label>
                    <textarea id="teknis_fasilitas" name="teknis_fasilitas" rows="3" class="form-control" placeholder="Catatan teknis dan fasilitas (ruang, perangkat, dll)..." required></textarea>
                    <div class="invalid-feedback">Pertanyaan ini wajib diisi</div>
                </div>

                <div class="mb-3">
                    <label for="panitia_pelayanan" class="form-label">Panitia dan Pelayanan</label>
                    <textarea id="panitia_pelayanan" name="panitia_pelayanan" rows="3" class="form-control" placeholder="Penilaian terhadap panitia dan pelayanan..." required></textarea>
                    <div class="invalid-feedback">Pertanyaan ini wajib diisi</div>
                </div>

                <div class="mb-3">
                    <label for="informasi_publikasi" class="form-label">Informasi dan Publikasi</label>
                    <textarea id="informasi_publikasi" name="informasi_publikasi" rows="3" class="form-control" placeholder="Komentar mengenai informasi dan publikasi acara..." required></textarea>
                    <div class="invalid-feedback">Pertanyaan ini wajib diisi</div>
                </div>

                <div class="mb-3">
                    <label for="kepuasan_peserta" class="form-label">Kepuasan Peserta</label>
                    <textarea id="kepuasan_peserta" name="kepuasan_peserta" rows="3" class="form-control" placeholder="Tingkat kepuasan peserta dan poin penting..." required></textarea>
                    <div class="invalid-feedback">Pertanyaan ini wajib diisi</div>
                </div>

                <div class="mb-3">
                    <label for="saran_masukan" class="form-label">Saran dan Masukan</label>
                    <textarea id="saran_masukan" name="saran_masukan" rows="3" class="form-control" placeholder="Saran dan masukan untuk perbaikan berikutnya..." required></textarea>
                    <div class="invalid-feedback">Pertanyaan ini wajib diisi</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" id="saveFeedbackBtn" class="btn btn-primary">Simpan Feedback</button>
                    <a href="{{ route('mitra.participation.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>

            <div id="feedbackErrorAlert" class="alert alert-danger mt-3 d-none" role="alert"></div>
            <div id="feedbackAlert" class="alert alert-success mt-3 d-none" role="alert">
                Feedback disimpan (placeholder).
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('feedbackForm');
    const btn = document.getElementById('saveFeedbackBtn');
    const feedbackAlert = document.getElementById('feedbackAlert');
    const feedbackErrorAlert = document.getElementById('feedbackErrorAlert');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // remove previous invalid states
        ['tujuan_manfaat','materi_narasumber','susunan_waktu','teknis_fasilitas','panitia_pelayanan','informasi_publikasi','kepuasan_peserta','saran_masukan'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.remove('is-invalid');
        });
        feedbackErrorAlert.classList.add('d-none');
        feedbackAlert.classList.add('d-none');

        const fields = [
            {id: 'tujuan_manfaat', label: 'Tujuan dan Manfaat Acara'},
            {id: 'materi_narasumber', label: 'Materi dan Narasumber'},
            {id: 'susunan_waktu', label: 'Susunan Acara dan Waktu'},
            {id: 'teknis_fasilitas', label: 'Teknis dan Fasilitas'},
            {id: 'panitia_pelayanan', label: 'Panitia dan Pelayanan'},
            {id: 'informasi_publikasi', label: 'Informasi dan Publikasi'},
            {id: 'kepuasan_peserta', label: 'Kepuasan Peserta'},
            {id: 'saran_masukan', label: 'Saran dan Masukan'},
        ];

        const missing = [];
        const payload = {};
        fields.forEach(f => {
            const el = document.getElementById(f.id);
            const val = el ? el.value.trim() : '';
            payload[f.id] = val;
            if (!val) missing.push(f.label);
        });

        if (missing.length > 0) {
            // show error and mark fields
            feedbackErrorAlert.classList.remove('d-none');
            feedbackErrorAlert.innerHTML = '<strong>Isi semua bagian berikut sebelum menyimpan:</strong><br>' + missing.map(s => '- ' + s).join('<br>');
            missing.forEach(label => {
                // find field by label mapping
                const f = fields.find(x => x.label === label);
                if (f) {
                    const el = document.getElementById(f.id);
                    if (el) el.classList.add('is-invalid');
                }
            });
            return;
        }

        // all filled — simulate save
        btn.setAttribute('disabled', 'disabled');
        console.log('Feedback payload:', payload);
        feedbackAlert.classList.remove('d-none');
        feedbackAlert.textContent = 'Feedback disimpan (placeholder).';
        setTimeout(() => { btn.removeAttribute('disabled'); }, 1000);
    });
});
</script>
@endpush
