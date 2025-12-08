@extends('layout.index')

@section('content')
<div class="container mt-4">
    <h2>Daftar Keikutsertaan Mitra</h2>
    <a href="{{ route('mitra.participation.create') }}" class="btn btn-primary mb-3">Tambah Keikutsertaan</a>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mitra</th>
                <th>Judul Pelatihan</th>
                <th>Tanggal Pelatihan</th>
                <th>Waktu Pelatihan</th>
                <th>Tempat Pelatihan</th>
                <th>Narasumber</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($participations as $participation)
            <tr>
                <td>{{ $participation->mitra->nama_perusahaan }}</td>
                <td>{{ $participation->judul_pelatihan }}</td>
                <td>{{ \Carbon\Carbon::parse($participation->tanggal_pelatihan)->format('d-m-Y') }}</td>
                <td>{{ $participation->waktu_pelatihan }}</td>
                <td>{{ $participation->tempat_pelatihan }}</td>
                <td>{{ $participation->narasumber }}</td>
                <td>{{ ucfirst($participation->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Belum ada data keikutsertaan mitra.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
