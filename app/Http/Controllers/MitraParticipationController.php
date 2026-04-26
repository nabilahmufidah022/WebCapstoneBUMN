<?php

namespace App\Http\Controllers;

use App\Models\MitraEventParticipation;
use App\Models\Mitra;
use App\Models\Feedback;
use App\Models\FeedbackReply;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MitraParticipationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = MitraEventParticipation::with('mitra')->orderBy('tanggal_pelatihan', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($user->usertype !== 'admin') {
            $mitra_id = Mitra::where('user_id', $user->id)->value('id');
            $query->where('mitra_id', $mitra_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_pelatihan', 'like', "%{$search}%")
                  ->orWhereHas('mitra', function ($m) use ($search) {
                      $m->where('nama_perusahaan', 'like', "%{$search}%");
                  });
            });
        }

        $participations = $query->get();

        // PERBAIKAN DI SINI: Mengambil mitra dengan status 'approved' (huruf kecil) atau 'Approved'
        $mitras = Mitra::whereIn('status', ['approved', 'Approved', '1'])->get();

        return view('partnership.participation_list', compact('participations', 'mitras'));
    }

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

        return redirect()->route('mitra.participation.index', ['status' => 'Akan Datang'])
                         ->with('success', 'Silabus pelatihan berhasil dijadwalkan.');
    }

    public function complete($id)
    {
        $participation = MitraEventParticipation::findOrFail($id);
        $participation->update(['status' => 'Selesai']);
        return redirect()->route('mitra.participation.index', ['status' => 'Selesai'])
                         ->with('success', 'Kegiatan ditandai Selesai.');
    }

    public function rate(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'catatan_internal' => 'nullable|string',
        ]);

        $p = MitraEventParticipation::findOrFail($id);
        $p->update([
            'rating' => $request->rating,
            'catatan_internal' => $request->catatan_internal,
        ]);

        $mitraId = $p->mitra_id;
        $mitra = Mitra::findOrFail($mitraId);
        $average = MitraEventParticipation::where('mitra_id', $mitraId)->whereNotNull('rating')->avg('rating');
        
        $totalIkut = MitraEventParticipation::where('mitra_id', $mitraId)
                      ->where('status', 'Selesai')
                      ->whereYear('tanggal_pelatihan', date('Y'))
                      ->count();

        $mitra->update([
            'average_rating' => $average,
            'status_aktif' => ($totalIkut >= 3) ? 'Aktif' : 'Non-Aktif'
        ]);

        return redirect()->back()->with('success', 'Penilaian disimpan.');
    }
}