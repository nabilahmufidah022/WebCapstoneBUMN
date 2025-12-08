@extends('layout.index')

@section('content')
<div class="container mt-4">
    <h2>Tambah Keikutsertaan Mitra</h2>
    <form action="{{ route('mitra.participation.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="mitra_id" class="form-label">Pilih Mitra</label>
            <select class="form-select @error('mitra_id') is-invalid @enderror" name="mitra_id" id="mitra_id" required>
                <option value="">-- Pilih Mitra --</option>
                @foreach($mitras as $mitra)
                    <option value="{{ $mitra->id }}" {{ old('mitra_id') == $mitra->id ? 'selected' : '' }}>
                        {{ $mitra->nama_perusahaan }}
                    </option>
                @endforeach
            </select>
            @error('mitra_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="judul_pelatihan" class="form-label">Judul Pelatihan</label>
            <input type="text" class="form-control @error('judul_pelatihan') is-invalid @enderror" id="judul_pelatihan" name="judul_pelatihan" value="{{ old('judul_pelatihan') }}" required>
            @error('judul_pelatihan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="tanggal_pelatihan" class="form-label">Tanggal Pelatihan</label>
            <input type="date" class="form-control @error('tanggal_pelatihan') is-invalid @enderror" id="tanggal_pelatihan" name="tanggal_pelatihan" value="{{ old('tanggal_pelatihan') }}" required>
            @error('tanggal_pelatihan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="waktu_pelatihan" class="form-label">Waktu Pelatihan</label>
            <input type="time" class="form-control @error('waktu_pelatihan') is-invalid @enderror" id="waktu_pelatihan" name="waktu_pelatihan" value="{{ old('waktu_pelatihan') }}" required>
            @error('waktu_pelatihan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="tempat_pelatihan" class="form-label">Tempat Pelatihan</label>
            <input type="text" class="form-control @error('tempat_pelatihan') is-invalid @enderror" id="tempat_pelatihan" name="tempat_pelatihan" value="{{ old('tempat_pelatihan') }}" required>
            @error('tempat_pelatihan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="narasumber" class="form-label">Narasumber</label>
            <input type="text" class="form-control @error('narasumber') is-invalid @enderror" id="narasumber" name="narasumber" value="{{ old('narasumber') }}" required>
            @error('narasumber')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select @error('status') is-invalid @enderror" name="status" id="status" required>
                <option value="">-- Pilih Status --</option>
                <option value="online" {{ old('status') == 'online' ? 'selected' : '' }}>Online</option>
                <option value="offline" {{ old('status') == 'offline' ? 'selected' : '' }}>Offline</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('mitra.participation.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
