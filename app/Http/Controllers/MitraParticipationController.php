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
        $mitra_id = Mitra::where('user_id', $user->id)->value('id');

        $query = MitraEventParticipation::with('mitra')->orderBy('tanggal_pelatihan', 'desc');

        // Non-admin users only see their own participations
        if ($user->usertype !== 'admin') {
            $query->where('mitra_id', $mitra_id);
        }

        // Search by judul_pelatihan or mitra nama_perusahaan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_pelatihan', 'like', "%{$search}%")
                  ->orWhereHas('mitra', function ($m) use ($search) {
                      $m->where('nama_perusahaan', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by participation kategori
        if ($request->filled('kategori')) {
            $kategori = $request->kategori;
            $query->where('kategori', $kategori);
        }
        
        if ($request->filled('tahun')) {
                $query->whereYear('created_at', $request->tahun);
            }

        $participations = $query->get();

        $mitras = Mitra::all();
        // $categories = Mitra::whereNotNull('kategori_mitra')->distinct()->pluck('kategori_mitra');

        return view('partnership.participation_list', compact('participations', 'mitras'));
    }

    /**
     * Export participations as CSV (admin only). Honors search, kategori, and tahun filters.
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->usertype !== 'admin') {
            abort(403);
        }

        $query = MitraEventParticipation::with('mitra')->orderBy('tanggal_pelatihan', 'desc');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_pelatihan', 'like', "%{$search}%")
                  ->orWhereHas('mitra', function ($m) use ($search) {
                      $m->where('nama_perusahaan', 'like', "%{$search}%");
                  });
            });
        }

        // Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Tahun (created_at)
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        $participations = $query->get();

        $filename = 'keikutsertaan_mitra_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = ['Nama Mitra', 'Judul Pelatihan', 'Tanggal Pelatihan', 'Waktu', 'Tempat', 'Narasumber', 'Status', 'Kategori', 'Didaftarkan Pada'];

        $callback = function () use ($participations, $columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);

            foreach ($participations as $p) {
                $row = [
                    $p->mitra->nama_perusahaan ?? '',
                    $p->judul_pelatihan,
                    $p->tanggal_pelatihan,
                    $p->waktu_pelatihan,
                    $p->tempat_pelatihan,
                    $p->narasumber,
                    $p->status,
                    $p->kategori ?? '',
                    $p->created_at->toDateTimeString(),
                ];
                fputcsv($out, $row);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
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
            'status' => 'required|in:online,offline',
            'kategori' => 'required|string',
        ]);

        MitraEventParticipation::create([
            'mitra_id' => $request->mitra_id,
            'judul_pelatihan' => $request->judul_pelatihan,
            'tanggal_pelatihan' => $request->tanggal_pelatihan,
            'waktu_pelatihan' => $request->waktu_pelatihan,
            'tempat_pelatihan' => $request->tempat_pelatihan,
            'narasumber' => $request->narasumber,
            'status' => $request->status,
            'kategori' => $request->kategori,
        ]);

        return redirect()
            ->route('mitra.participation.index')
            ->with('success', 'Data keikutsertaan mitra berhasil ditambahkan.');
    }

    //detail keikutsertaan mitra
    public function show($id)
    {
        $user = Auth::user();

        $query = MitraEventParticipation::with(['mitra', 'feedbacks.replies.user', 'feedbacks.user']);
        if ($user->usertype !== 'admin') {
            $mitra_id = Mitra::where('user_id', $user->id)->value('id');
            $query->where('mitra_id', $mitra_id);
        }

        $participation = $query->findOrFail($id);

        return view('partnership.participation_detail', compact('participation'));
    }

    // feedback form/view for a participation
    public function feedback($id)
    {
        $user = Auth::user();

        $query = MitraEventParticipation::with(['mitra', 'feedbacks.replies.user', 'feedbacks.user']);
        if ($user->usertype !== 'admin') {
            $mitra_id = Mitra::where('user_id', $user->id)->value('id');
            $query->where('mitra_id', $mitra_id);
        }

        $participation = $query->findOrFail($id);

        return view('partnership.participation_feedback', compact('participation'));
    }

    // store feedback for a participation
    public function storeFeedback(Request $request, $id)
    {
        $user = Auth::user();

        $request->validate([
            'tujuan_manfaat' => 'required|string',
            'materi_narasumber' => 'required|string',
            'susunan_waktu' => 'required|string',
            'teknis_fasilitas' => 'required|string',
            'panitia_pelayanan' => 'required|string',
            'informasi_publikasi' => 'required|string',
            'kepuasan_peserta' => 'required|string',
            'saran_masukan' => 'required|string',
        ]);

        $participation = MitraEventParticipation::findOrFail($id);

        Feedback::create([
            'mitra_event_participation_id' => $participation->id,
            'user_id' => $user ? $user->id : null,
            'tujuan_manfaat' => $request->tujuan_manfaat,
            'materi_narasumber' => $request->materi_narasumber,
            'susunan_waktu' => $request->susunan_waktu,
            'teknis_fasilitas' => $request->teknis_fasilitas,
            'panitia_pelayanan' => $request->panitia_pelayanan,
            'informasi_publikasi' => $request->informasi_publikasi,
            'kepuasan_peserta' => $request->kepuasan_peserta,
            'saran_masukan' => $request->saran_masukan,
        ]);

        return redirect()->route('mitra.participation.feedback', $participation->id)->with('success', 'Feedback berhasil disimpan.');
    }

    // admin reply to a feedback
    public function replyFeedback(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user || $user->usertype !== 'admin') {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        $feedback = Feedback::findOrFail($id);

        FeedbackReply::create([
            'feedback_id' => $feedback->id,
            'user_id' => $user->id,
            'message' => $request->message,
        ]);

        return redirect()->back()->with('success', 'Balasan terkirim.');
    }

    public function destroy($id)
    {
        $participation = MitraEventParticipation::findOrFail($id);
        $participation->delete();

        return redirect()
            ->route('mitra.participation.index')
            ->with('success', 'Keikutsertaan mitra berhasil dihapus.');
    }

}
