@extends('layout.index')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">Keikutsertaan Mitra</h4>
            <p class="text-muted mb-0">
                Daftar keikutsertaan mitra dalam kegiatan pelatihan
            </p>
        </div>

        @if(auth()->user()->usertype === 'admin')
        <a href="#"
            class="btn btn-primary rounded-pill px-4"
            data-bs-toggle="modal"
            data-bs-target="#addParticipationModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Keikutsertaan
        </a>
        @endif
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="table-responsive">
            <table class="table align-middle mb-0 participation-table">
                <thead>
                    <tr class="text-muted small">
                        <th>Nama Mitra</th>
                        <th>Judul Pelatihan</th>
                        <th>Tanggal Pelatihan</th>
                        <th>Waktu</th>
                        <th>Tempat</th>
                        <th>Narasumber</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($participations as $item)
                    <tr>
                        <td class="fw-semibold">
                            {{ $item->mitra->nama_perusahaan }}
                        </td>

                        <td>
                            {{ $item->judul_pelatihan }}
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($item->tanggal_pelatihan)->translatedFormat('d F Y') }}
                        </td>

                        <td>
                            {{ $item->waktu_pelatihan }} WIB
                        </td>

                        <td>
                            {{ $item->tempat_pelatihan }}
                        </td>

                        <td>
                            {{ $item->narasumber }}
                        </td>

                        <td>
                            @if($item->status = 'online')
                                <span class="badge badge-online">Online</span>
                            @else
                                <span class="badge badge-offline">Offline</span>
                            @endif
                        </td>

                        <td class="text-center">
                            <a href="{{ route('mitra.participation.show', $item->id) }}"
                               class="text-primary text-decoration-none fw-semibold">
                                <i class="bi bi-eye me-1"></i> Detail
                            </a>

                            
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Belum ada data keikutsertaan mitra
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL FORM -->
<div class="modal fade" id="addParticipationModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('mitra.participation.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Keikutsertaan Mitra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label>Mitra</label>
                    <select name="mitra_id" class="form-select" required>
                        <option value="">-- Pilih Mitra --</option>
                        @foreach($mitras as $mitra)
                            @if($mitra->status == 1)
                                <option value="{{ $mitra->id }}">{{ $mitra->nama_perusahaan }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Judul Pelatihan</label>
                    <input type="text" name="judul_pelatihan" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Tanggal Pelatihan</label>
                    <input type="date" name="tanggal_pelatihan" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Waktu Pelatihan</label>
                    <input type="time" name="waktu_pelatihan" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Tempat Pelatihan</label>
                    <input type="text" name="tempat_pelatihan" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Narasumber</label>
                    <input type="text" name="narasumber" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" class="form-select" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="online">Online</option>
                        <option value="offline">Offline</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Auto open modal if validation error --}}
@if ($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function () {
        new bootstrap.Modal(document.getElementById('addParticipationModal')).show();
    });
</script>
@endif
@endsection
