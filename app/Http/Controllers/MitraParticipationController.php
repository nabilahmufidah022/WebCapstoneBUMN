<?php

namespace App\Http\Controllers;

use App\Models\MitraEventParticipation;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MitraParticipationController extends Controller
{
    /**
     * Tampilkan daftar Silabus/Agenda.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Query dasar dengan relasi mitra - Diurutkan berdasarkan tanggal terdekat
        $query = MitraEventParticipation::with('mitra')
            ->orderBy('tanggal_pelatihan', 'asc');

        // 1. FILTER OTOMATIS BERDASARKAN ROLE
        if ($user->usertype !== 'admin') {
            $mitra_id = Mitra::where('user_id', $user->id)->value('id');
            $query->where('mitra_id', $mitra_id);
        }

        // 2. FILTER STATUS
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. FILTER PENCARIAN
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('judul_pelatihan', 'like', "%{$search}%")
                    ->orWhereHas('mitra', function ($m) use ($search) {
                        $m->where('nama_perusahaan', 'like', "%{$search}%");
                    });
            });
        }

        // 4. FILTER KATEGORI
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $participations = $query->get();

        // Ambil daftar mitra untuk dropdown modal
        $mitras = Mitra::whereIn('status', [
            'approved',
            'Approved',
            '1',
            1,
            2
        ])->get();

        return view(
            'partnership.participation_list',
            compact('participations', 'mitras', 'user')
        );
    }

    /**
     * Simpan Agenda Baru (Admin Only).
     */
    public function store(Request $request)
    {
        $request->validate([
            'mitra_id' => 'required|exists:mitra,id',
            'judul_pelatihan' => 'required|string|max:255',
            'tanggal_pelatihan' => 'required|date',
            'waktu_pelatihan' => 'required',
            'tempat_pelatihan' => 'required|string|max:255',
            'narasumber' => 'required|string|max:255',
            'pelaksanaan' => 'required|in:online,offline',
            'kategori' => 'required|string',
        ]);

        MitraEventParticipation::create([
            'mitra_id' => $request->mitra_id,
            'judul_pelatihan' => $request->judul_pelatihan,
            'tanggal_pelatihan' => $request->tanggal_pelatihan,
            'waktu_pelatihan' => $request->waktu_pelatihan,
            'tempat_pelatihan' => $request->tempat_pelatihan,
            'narasumber' => $request->narasumber,
            'pelaksanaan' => $request->pelaksanaan,
            'kategori' => $request->kategori,
            'status' => 'Akan Datang',
        ]);

        return redirect()
            ->route('mitra.participation.index')
            ->with(
                'success',
                'Jadwal silabus baru berhasil ditambahkan untuk seluruh periode.'
            );
    }

    /**
     * Memproses pembaruan data silabus (EDIT).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul_pelatihan' => 'required|string|max:255',
            'tanggal_pelatihan' => 'required|date',
            'waktu_pelatihan' => 'required',
            'tempat_pelatihan' => 'required|string|max:255',
            'narasumber' => 'required|string|max:255',
            'pelaksanaan' => 'required|in:online,offline',
            'kategori' => 'required|string',
        ]);

        $participation = MitraEventParticipation::findOrFail($id);

        $participation->update([
            'judul_pelatihan' => $request->judul_pelatihan,
            'tanggal_pelatihan' => $request->tanggal_pelatihan,
            'waktu_pelatihan' => $request->waktu_pelatihan,
            'tempat_pelatihan' => $request->tempat_pelatihan,
            'narasumber' => $request->narasumber,
            'pelaksanaan' => $request->pelaksanaan,
            'kategori' => $request->kategori,
        ]);

        return redirect()
            ->route('mitra.participation.index')
            ->with('success', 'Data agenda berhasil diperbarui!');
    }

    /**
     * Menampilkan Halaman Feedback untuk Mitra.
     */
    public function feedback($id)
    {
        $participation = MitraEventParticipation::with('mitra')->findOrFail($id);

        return view(
            'partnership.feedback_mitra',
            compact('participation')
        );
    }

    /**
     * Menyimpan Feedback dari Mitra.
     */
    public function storeFeedback(Request $request, $id)
    {
        $request->validate([
            'kepuasan' => 'required|integer|min:1|max:5',
            'saran' => 'required|string',
        ]);

        $participation = MitraEventParticipation::findOrFail($id);

        $participation->update([
            'feedback_mitra' => $request->saran,
            'rating_mitra' => $request->kepuasan,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Terima kasih atas feedback Anda!');
    }

    /**
     * Tampilkan Halaman Penilaian Internal (Admin).
     */
    public function complete($id)
    {
        $participation = MitraEventParticipation::with('mitra')->findOrFail($id);

        return view(
            'partnership.rate_participation',
            compact('participation')
        );
    }

    /**
     * Simpan Rating Internal Admin dan Selesaikan Agenda.
     */
    public function rate(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'catatan_internal' => 'nullable|string',
        ]);

        $participation = MitraEventParticipation::findOrFail($id);

        $participation->update([
            'rating' => $request->rating,
            'catatan_internal' => $request->catatan_internal,
            'status' => 'Selesai',
        ]);

        $mitra = Mitra::find($participation->mitra_id);

        if ($mitra) {
            $average = MitraEventParticipation::where('mitra_id', $mitra->id)
                ->whereNotNull('rating')
                ->avg('rating');

            $totalSelesai = MitraEventParticipation::where('mitra_id', $mitra->id)
                ->where('status', 'Selesai')
                ->count();

            $mitra->update([
                'average_rating' => $average,
                'status_aktif' => ($totalSelesai >= 3) ? 'Aktif' : 'Non-Aktif',
            ]);
        }

        return redirect()
            ->route('mitra.participation.index')
            ->with(
                'success',
                'Agenda telah diselesaikan dan penilaian berhasil disimpan.'
            );
    }

    /**
     * Tampilkan Detail Agenda.
     */
    public function show($id)
    {
        $participation = MitraEventParticipation::with([
            'mitra',
            'feedbacks.replies.user',
        ])->findOrFail($id);

        return view(
            'partnership.participation_detail',
            compact('participation')
        );
    }

    /**
     * Hapus Data Silabus.
     */
    public function destroy($id)
    {
        $data = MitraEventParticipation::findOrFail($id);

        $data->delete();

        return redirect()
            ->route('mitra.participation.index')
            ->with('success', 'Data silabus berhasil dihapus.');
    }
}
