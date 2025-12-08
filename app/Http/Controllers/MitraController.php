<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\HistoryMitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MitraController extends Controller
{
    public function goListMitra(){
        $user = Auth::user();
        if ($user->usertype == 'admin') {
            $mitras = Mitra::with('user')->get();
        } else {
            $mitras = Mitra::with('user')->where('user_id', Auth::id())->get();
        }
        $hasMitra = $user->mitra()->exists();
        return view('partnership.list_mitra', compact('user','mitras', 'hasMitra'));
    }

    public function goDaftarMitra(){
        $user = Auth::user();
        $hasMitra = $user->mitra()->exists();
        return view('partnership.daftar_mitra', compact('user', 'hasMitra'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
            'nama_perusahaan' => 'required|string|max:255',
            'lokasi_perusahaan' => 'required|string|max:255',
            'deskripsi_perusahaan' => 'required|string',
            'company_profile' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB max
            'surat_permohonan_audiensi' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'nama_lengkap' => $request->nama_lengkap,
            'no_telepon' => $request->no_telepon,
            'nama_perusahaan' => $request->nama_perusahaan,
            'lokasi_perusahaan' => $request->lokasi_perusahaan,
            'deskripsi_perusahaan' => $request->deskripsi_perusahaan,
            'status' => 0,
        ];

        if ($request->hasFile('company_profile')) {
            $fileName = time() . '_company_profile.' . $request->company_profile->extension();
            $request->company_profile->move(public_path('uploads/mitra'), $fileName);
            $data['company_profile'] = $fileName;
        }

        if ($request->hasFile('surat_permohonan_audiensi')) {
            $fileName = time() . '_surat_permohonan.' . $request->surat_permohonan_audiensi->extension();
            $request->surat_permohonan_audiensi->move(public_path('uploads/mitra'), $fileName);
            $data['surat_permohonan_audiensi'] = $fileName;
        }

        $mitra = Mitra::create($data);

        // Log history
        HistoryMitra::create([
            'mitra_id' => $mitra->id,
            'action' => 'registered',
            'description' => 'Mitra baru didaftarkan oleh user.',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('list_mitra')->with('success', 'Mitra berhasil didaftarkan.');
    }

    public function approve($id)
    {
        $mitra = Mitra::findOrFail($id);
        $mitra->update(['status' => 1]);

        // Log history
        HistoryMitra::create([
            'mitra_id' => $mitra->id,
            'action' => 'approved',
            'description' => 'Mitra disetujui oleh admin.',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('list_mitra')->with('success', 'Mitra berhasil disetujui.');
    }

    public function reject($id)
    {
        $mitra = Mitra::findOrFail($id);
        $mitra->update(['status' => 2]);

        // Log history
        HistoryMitra::create([
            'mitra_id' => $mitra->id,
            'action' => 'rejected',
            'description' => 'Mitra ditolak oleh admin.',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('list_mitra')->with('success', 'Mitra berhasil ditolak.');
    }

    public function detail($id)
    {
        $mitra = Mitra::with('user')->findOrFail($id);
        $histories = HistoryMitra::with('user')->where('mitra_id', $id)->orderBy('created_at', 'desc')->get();
        return view('partnership.detail_mitra', compact('mitra', 'histories'));
    }
}
