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
                $section = request('section', 'all');

                // Mode logic
                $isViewMode = ($section === 'all'); // Blue Button
                $isInputMode = ($section !== 'all'); // Green Button

                // Show form if in Input Mode AND not submitted yet (for User)
                $showForm = $isInputMode && !$userHasSubmitted;

                // Logic for Admin Form: Show if Admin AND Input Mode
                $showAdminForm = $isAdmin && $isInputMode;

                $displayFeedbacks = $participation->feedbacks;

                // Restore variables for display logic
                $showUserContent = ($section === 'user' || $section === 'all' || $section === 'admin');
                $showAdminContent = ($section === 'admin' || $section === 'all');
            @endphp

            @if(session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif

            {{-- ADMIN FEEDBACK FORM (Visible only in Input Mode for Admin) --}}
            @if($showAdminForm)
            <div class="card border-primary shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Kirim Feedback / Pesan untuk Mitra</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('mitra.participation.feedback.reply', $participation->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea name="message" class="form-control" rows="4" placeholder="Tulis evaluasi, pesan, atau feedback untuk mitra di sini..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-send me-2"></i>Kirim Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- USER FEEDBACK FORM (Visible only in Input Mode for Mitra) --}}
            @if(auth()->check() && $showForm && !$isAdmin)
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
                    {{-- Go back to detail page --}}
                    <a href="{{ route('mitra.participation.show', $participation->id) }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
            @endif

            {{-- FEEDBACK LIST (Only in View Mode) --}}
            @if($isViewMode)
            @php
                // No filtering by user_id here.
                // We want Mitra to see Admin's feedback, and Admin to see Mitra's feedback.
                // Since the participation is already scoped to the Mitra (in Controller),
                // all feedbacks attached to this participation are relevant.
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
                                        @if($feedback->user && $feedback->user->usertype === 'admin')
                                            <span class="badge bg-primary mb-1">Feedback dari Admin (ke Mitra)</span><br>
                                            <strong>{{ $feedback->user->name }}</strong>
                                        @else
                                            <span class="badge bg-success mb-1">Feedback dari Mitra (ke Admin)</span><br>
                                            <strong>{{ $feedback->user ? $feedback->user->name : 'Anonim' }}</strong>
                                        @endif
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
            @endif
        </div>
    </div>
</div>
@endsection

{{-- submission script removed because form was removed --}}
