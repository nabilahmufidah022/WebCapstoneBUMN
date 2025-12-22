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
            @php
                $userHasSubmitted = $participation->feedbacks->contains('user_id', auth()->id());
                $isAdmin = auth()->user()->usertype === 'admin';
                $showForm = !$isAdmin && !$userHasSubmitted;

                $section = request('section', 'all');
                $showUserContent = ($section === 'user' || $section === 'all' || $section === 'admin');
                $showAdminContent = ($section === 'admin' || $section === 'all');
            @endphp

            @if(session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif

            {{-- Feedback submission form (shown when user can submit and hasn't yet, and section is user/all) --}}
            @if(auth()->check() && $showForm && $showUserContent)
            <form method="POST" action="{{ route('mitra.participation.feedback.store', $participation->id) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label"><strong>Tujuan & Manfaat:</strong></label>
                    <textarea name="tujuan_manfaat" rows="3" class="form-control" placeholder="Jelaskan tujuan dan manfaat acara..." required>{{ old('tujuan_manfaat') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Materi & Narasumber:</strong></label>
                    <textarea name="materi_narasumber" rows="3" class="form-control" placeholder="Tuliskan materi dan penilaian narasumber..." required>{{ old('materi_narasumber') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Susunan & Waktu:</strong></label>
                    <textarea name="susunan_waktu" rows="3" class="form-control" placeholder="Komentar mengenai susunan acara dan waktu..." required>{{ old('susunan_waktu') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Teknis & Fasilitas:</strong></label>
                    <textarea name="teknis_fasilitas" rows="3" class="form-control" placeholder="Catatan teknis dan fasilitas (ruang, perangkat, dll)..." required>{{ old('teknis_fasilitas') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Panitia & Pelayanan:</strong></label>
                    <textarea name="panitia_pelayanan" rows="3" class="form-control" placeholder="Penilaian terhadap panitia dan pelayanan..." required>{{ old('panitia_pelayanan') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Informasi & Publikasi:</strong></label>
                    <textarea name="informasi_publikasi" rows="3" class="form-control" placeholder="Komentar mengenai informasi dan publikasi acara..." required>{{ old('informasi_publikasi') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Kepuasan Peserta:</strong></label>
                    <textarea name="kepuasan_peserta" rows="3" class="form-control" placeholder="Tingkat kepuasan peserta dan poin penting..." required>{{ old('kepuasan_peserta') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Saran & Masukan:</strong></label>
                    <textarea name="saran_masukan" rows="3" class="form-control" placeholder="Saran dan masukan untuk perbaikan berikutnya..." required>{{ old('saran_masukan') }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Simpan Feedback</button>
                    <a href="{{ route('mitra.participation.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>

            @endif
            

            
            @php
                $displayFeedbacks = $participation->feedbacks;
                if (auth()->check()) {
                    // If regular user, only show their own
                    if (auth()->user()->usertype !== 'admin') {
                        $displayFeedbacks = $displayFeedbacks->where('user_id', auth()->id());
                    } 
                    // If Admin (or anyone) and section is 'user', maybe we only want to see "User Feedbacks" (from Mitra)
                    // If there are feedbacks created by admins (testing?), hide them?
                    // Let's assume we filter out admin-authored feedbacks generally if the goal is "Feedback dari User"
                    $displayFeedbacks = $displayFeedbacks->filter(function($f) { 
                         return $f->user && $f->user->usertype !== 'admin'; 
                    });
                }
            @endphp
            
            @if(auth()->check())
            {{-- Existing feedbacks and replies --}}
            <div class="mt-4">
                <h5>Feedback Tersimpan</h5>
                @if($displayFeedbacks->isEmpty())
                    <div class="alert alert-info mt-3" role="alert">Belum ada feedback untuk kegiatan ini.</div>
                @else
                    <div class="list-group">
                        @foreach($displayFeedbacks as $feedback)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $feedback->user ? $feedback->user->name : 'Anonim' }}</strong>
                                        <small class="text-muted"> — {{ $feedback->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>

                                @if($showUserContent)
                                <div class="mt-2">
                                    <p><strong>Tujuan & Manfaat:</strong><br>{{ $feedback->tujuan_manfaat }}</p>
                                    <p><strong>Materi & Narasumber:</strong><br>{{ $feedback->materi_narasumber }}</p>
                                    <p><strong>Susunan & Waktu:</strong><br>{{ $feedback->susunan_waktu }}</p>
                                    <p><strong>Teknis & Fasilitas:</strong><br>{{ $feedback->teknis_fasilitas }}</p>
                                    <p><strong>Panitia & Pelayanan:</strong><br>{{ $feedback->panitia_pelayanan }}</p>
                                    <p><strong>Informasi & Publikasi:</strong><br>{{ $feedback->informasi_publikasi }}</p>
                                    <p><strong>Kepuasan Peserta:</strong><br>{{ $feedback->kepuasan_peserta }}</p>
                                    <p><strong>Saran & Masukan:</strong><br>{{ $feedback->saran_masukan }}</p>
                                </div>
                                @endif

                                {{-- Admin Response Section (Fetched from Database) --}}
                                @if($showAdminContent)
                                <div class="mt-3">
                                    <h6 class="fw-bold text-dark"><i class="bi bi-arrow-return-right me-1"></i> Respon Admin</h6>
                                    @if($feedback->replies->isEmpty())
                                        <div class="alert alert-secondary py-2 px-3 mb-0 small text-muted">Belum ada respon dari admin.</div>
                                    @else
                                        @foreach($feedback->replies as $reply)
                                            <div class="card border-0 bg-light mb-2">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <strong class="text-primary me-2">{{ $reply->user ? $reply->user->name : 'Admin' }}</strong>
                                                            <span class="badge bg-primary rounded-pill" style="font-size: 0.6rem;">Admin</span>
                                                        </div>
                                                        <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                                    </div>
                                                    <p class="mb-0 text-dark">{{ $reply->message }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                @endif

                                @if(auth()->user()->usertype === 'admin' && $showAdminContent)
                                <form action="{{ route('mitra.participation.feedback.reply', $feedback->id) }}" method="POST" class="mt-3">
                                    @csrf
                                    <div class="mb-2">
                                        <textarea name="message" class="form-control" rows="3" placeholder="Tulis balasan untuk feedback ini..." required></textarea>
                                    </div>
                                    <button class="btn btn-primary btn-sm">Kirim Balasan</button>
                                </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

{{-- submission script removed because form was removed --}}
