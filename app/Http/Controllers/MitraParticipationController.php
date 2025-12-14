<?php

namespace App\Http\Controllers;

use App\Models\MitraEventParticipation;
use App\Models\Mitra;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MitraParticipationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mitra_id = Mitra::where('user_id', $user->id)->value('id');


        if ($user->usertype === 'admin') {
            $participations = MitraEventParticipation::with('mitra')
                ->orderBy('tanggal_pelatihan', 'desc')
                ->get();
        } 
        else {
            $participations = MitraEventParticipation::with('mitra')
                ->where('mitra_id', $mitra_id)
                ->orderBy('tanggal_pelatihan', 'desc')
                ->get();
        }

        $mitras = Mitra::all();

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
            'status' => 'required|in:online,offline',
        ]);

        MitraEventParticipation::create([
            'mitra_id' => $request->mitra_id,
            'judul_pelatihan' => $request->judul_pelatihan,
            'tanggal_pelatihan' => $request->tanggal_pelatihan,
            'waktu_pelatihan' => $request->waktu_pelatihan,
            'tempat_pelatihan' => $request->tempat_pelatihan,
            'narasumber' => $request->narasumber,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('mitra.participation.index')
            ->with('success', 'Data keikutsertaan mitra berhasil ditambahkan.');
    }

    //detail keikutsertaan mitra
    public function show($id)
    {
        $user = Auth::user();

        $query = MitraEventParticipation::with('mitra');
        if ($user->usertype !== 'admin') {
            $mitra_id = Mitra::where('user_id', $user->id)->value('id');
            $query->where('mitra_id', $mitra_id);
        }

        $participation = $query->findOrFail($id);

        return view('partnership.participation_detail', compact('participation'));
    }
}
