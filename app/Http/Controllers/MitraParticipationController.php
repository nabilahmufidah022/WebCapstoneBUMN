<?php

namespace App\Http\Controllers;

use App\Models\MitraEventParticipation;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MitraParticipationController extends Controller
{
    public function index()
    {
        $participations = MitraEventParticipation::with('mitra')->orderBy('tanggal_pelatihan', 'desc')->get();
        return view('partnership.participation_list', compact('participations'));
    }

    public function create()
    {
        $mitras = Mitra::all();
        return view('partnership.participation_form', compact('mitras'));
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

        MitraEventParticipation::create($request->all());

        return redirect()->route('mitra.participation.index')->with('success', 'Data keikutsertaan mitra berhasil disimpan.');
    }
}
