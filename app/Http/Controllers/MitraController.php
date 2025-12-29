<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\HistoryMitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MitraController extends Controller
{
    /**
     * Halaman Daftar Mitra
     * Admin: Melihat semua mitra yang disetujui (Status 1) dengan filter
     * User: Hanya melihat status pendaftaran miliknya sendiri
     */
    public function goListMitra(Request $request)
    {
        $user = Auth::user();

        if ($user->usertype == 'admin') {
            // LOGIKA ADMIN: Ambil semua mitra yang sudah disetujui (status 1)
            $query = Mitra::with('user')->where('status', 1);

            // Filter Search (Nama Perusahaan atau Email)
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('nama_perusahaan', 'like', '%' . $request->search . '%')
                      ->orWhereHas('user', function($u) use ($request) {
                          $u->where('email', 'like', '%' . $request->search . '%');
                      });
                });
            }

            // Filter Kategori Mitra (Jika ada kolom kategori_mitra)
            if ($request->filled('kategori')) {
                $query->where('kategori_mitra', $request->kategori);
            }

            // Filter Tahun
            if ($request->filled('tahun')) {
                $query->whereYear('created_at', $request->tahun);
            }

            $mitras = $query->orderBy('created_at', 'desc')->get();
        } else {
            // LOGIKA USER: Hanya ambil data milik user yang sedang login
            $mitras = Mitra::with('user')
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $hasMitra = $user->mitra()->exists();

        return view('partnership.list_mitra', compact('user', 'mitras', 'hasMitra'));
    }

    /**
     * Halaman Kelola Pendaftaran (Khusus Admin)
     * Menampilkan pendaftaran yang masih berstatus 0 (Pending)
     */
    public function goKelolaPendaftaran()
    {
        $user = Auth::user();

        if ($user->usertype != 'admin') {
            return redirect()->route('dashboard');
        }

        // Ambil mitra yang statusnya 0 (Pending) untuk diverifikasi
        $mitras = Mitra::with('user')->where('status', 0)->orderBy('created_at', 'asc')->get();

        return view('partnership.kelola_pendaftaran', compact('user', 'mitras'));
    }

    public function goDaftarMitra()
    {
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
            
            // PERBAIKAN: Ubah validasi agar sesuai nama input di Form (bidang_perusahaan)
            'bidang_perusahaan' => 'required|string|max:255', 
            
            'lokasi_perusahaan' => 'required|string|max:255',
            'deskripsi_perusahaan' => 'required|string',
            'company_profile' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'surat_permohonan_audiensi' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'nama_lengkap' => $request->nama_lengkap,
            'no_telepon' => $request->no_telepon,
            'nama_perusahaan' => $request->nama_perusahaan,
            
            // PERBAIKAN: Ambil dari request->bidang_perusahaan
            'bidang_perusahaan' => $request->bidang_perusahaan, 

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

        HistoryMitra::create([
            'mitra_id' => $mitra->id,
            'action' => 'registered',
            'description' => 'Mitra baru didaftarkan oleh user.',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('list_mitra')->with('success', 'Pendaftaran berhasil dikirim. Menunggu verifikasi admin.');
    }

    public function approve($id)
    {
        $mitra = Mitra::findOrFail($id);
        $mitra->update(['status' => 1]);

        HistoryMitra::create([
            'mitra_id' => $mitra->id,
            'action' => 'approved',
            'description' => 'Mitra disetujui oleh admin.',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('kelola_pendaftaran')->with('success', 'Mitra berhasil disetujui.');
    }

    public function reject($id)
    {
        $mitra = Mitra::findOrFail($id);
        $mitra->update(['status' => 2]);

        HistoryMitra::create([
            'mitra_id' => $mitra->id,
            'action' => 'rejected',
            'description' => 'Mitra ditolak oleh admin.',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('kelola_pendaftaran')->with('success', 'Mitra berhasil ditolak.');
    }

    public function detail($id)
    {
        $mitra = Mitra::with('user')->findOrFail($id);
        $histories = HistoryMitra::with('user')->where('mitra_id', $id)->orderBy('created_at', 'desc')->get();
        return view('partnership.detail_mitra', compact('mitra', 'histories'));
    }

    /**
     * Export mitra list as CSV (admin only). Honors same filters as goListMitra.
     */
    public function exportListMitra(Request $request)
    {
        $user = Auth::user();
        if ($user->usertype != 'admin') {
            abort(403);
        }

        $query = Mitra::with('user')->where('status', 1);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_perusahaan', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($u) use ($request) {
                      $u->where('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori_mitra', $request->kategori);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        $mitras = $query->orderBy('created_at', 'desc')->get();

        $filename = 'mitra_export_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        // Header CSV
        $columns = ['Nama Perusahaan', 'Bidang Mitra', 'Nama PIC', 'Email PIC', 'No. HP PIC', 'Lokasi', 'Deskripsi Perusahaan', 'Didaftarkan Pada'];

        $callback = function() use ($mitras, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($mitras as $m) {
                $row = [
                    $m->nama_perusahaan,
                    $m->bidang_perusahaan?? '-', // UPDATE: Pastikan ini bidang_perusahaan
                    $m->nama_lengkap ?? '',
                    $m->user->email ?? '',
                    $m->no_telepon ?? '',
                    $m->lokasi_perusahaan,
                    $m->deskripsi_perusahaan ?? '',
                    $m->created_at->toDateTimeString(),
                ];
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}