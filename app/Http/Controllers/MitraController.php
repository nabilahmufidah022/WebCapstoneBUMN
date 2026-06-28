<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\HistoryMitra;
use App\Models\MitraEventParticipation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MitraController extends Controller
{
    /**
     * Halaman Daftar Mitra (Pusat Data / Dashboard Tracking)
     * Digunakan oleh Admin untuk memantau seluruh mitra secara realtime.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentYear = Carbon::now()->year;

        /**
         * INTEGRASI REALTIME:
         * Mengambil data mitra (Status 2 / Approved) dengan hitungan partisipasi
         * langsung dari tabel silabus (MitraEventParticipation).
         */
        $query = Mitra::with('user')
            ->where('status', 2)
            ->withCount('mitraEventParticipations');

        // Logic Filter Pencarian (Nama Perusahaan atau PIC)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_perusahaan', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_lengkap', 'like', '%' . $request->search . '%');
            });
        }

        // FITUR REVISI DOSEN: LOGIC FILTER BERDASARKAN QUERY DATA JSON ARRAY
        if ($request->filled('bidang')) {
            $query->whereJsonContains('bidang_perusahaan', $request->bidang);
        }

        // Logic Filter Tahun Bergabung
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        // Logic Sorting & Filtering Khusus
        if ($request->sort == 'keaktifan_low') {
            // 🌟 KUNCI FILTER: Hanya meloloskan mitra yang total keterlibatannya MAKSIMAL 2 PELATIHAN (0, 1, atau 2 sesi)
            $query->having('mitra_event_participations_count', '<=', 2)
                  ->orderBy('mitra_event_participations_count', 'asc');
        } elseif ($request->sort == 'rating_high') {
            // Mengeliminasi data bernilai 0 atau null dari list rekomendasi rating tertinggi
            $query->whereNotNull('average_rating')
                  ->where('average_rating', '>', 0)
                  ->orderBy('average_rating', 'desc');
        } else {
            // Default: Data terbaru
            $query->latest();
        }

        $mitras = $query->get();

        /**
         * FITUR 3: LOGIKA STATUS OTOMATIS (AUDIT TAHUNAN)
         * 1. Mitra yang baru gabung di tahun berjalan (currentYear) otomatis AKTIF.
         * 2. Mitra lama diaudit berdasarkan keterlibatan tahun sebelumnya (currentYear - 1).
         * 3. Jika tahun lalu tidak mencapai minimal 3 pelatihan, maka NON-AKTIF.
         */
        foreach ($mitras as $mitra) {
            $tahunGabung = $mitra->created_at->year;

            if ($tahunGabung >= $currentYear) {
                // KONDISI A: Mitra Baru (Masa Tenggang 1 Tahun)
                $mitra->status_aktif = 'Aktif';
                $mitra->is_active = true;
            } else {
                // KONDISI B: Mitra Lama (Audit Berdasarkan Tahun Lalu)
                $lastYear = $currentYear - 1;
                $countLastYear = $mitra->mitraEventParticipations()
                                       ->whereYear('tanggal_pelatihan', $lastYear)
                                       ->count();

                if ($countLastYear < 3) {
                    $mitra->status_aktif = 'Non-Aktif';
                    $mitra->is_active = false;
                } else {
                    $mitra->status_aktif = 'Aktif';
                    $mitra->is_active = true;
                }
            }
            $mitra->save();
        }

        // Data Statistik Realtime untuk Dashboard Tracking
        $totalMitra = Mitra::count();
        $pendingMitra = Mitra::where('status', 0)->count();
        $approvedMitra = Mitra::where('status', 2)->count();
        $totalParticipations = MitraEventParticipation::count();
        $recentParticipations = MitraEventParticipation::with('mitra')
                                ->latest()
                                ->take(5)
                                ->get();

        return view('partnership.list_mitra', compact(
            'mitras',
            'totalMitra',
            'pendingMitra',
            'approvedMitra',
            'totalParticipations',
            'recentParticipations',
            'user'
        ));
    }

    /**
     * 🌟 MANAJEMEN DATA BARU: Memproses pembaruan data akun & profil bisnis mandiri oleh Mitra.
     * Mengakomodasi pembukaan gembok fields yang diubah dari readonly.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // 1. Validasi Inputan Akun Utama & Data Kemitraan
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'no_telepon' => 'required|regex:/^[0-9]+$/|max:20',
            'nama_lengkap' => 'required|string|max:255',
            'bidang_perusahaan' => 'required|string',
            'deskripsi_perusahaan' => 'required|string',
            'lokasi_perusahaan' => 'required|string',
        ]);

        // 2. Proses Update Data Akun User (Name & Email)
        $user->name = $request->name;
        $user->email = $request->email;

        // Penanganan Unggah Foto Profil Baru
        if ($request->hasFile('profile_image')) {
            $imageName = time() . '_avatar.' . $request->profile_image->extension();
            $request->profile_image->move(public_path('img'), $imageName);
            $user->profile_image = $imageName;
        }
        $user->save();

        // 3. Proses Update Data Profil Entitas Bisnis Mitra (Jika Data Terdaftar)
        $mitra = Mitra::where('user_id', $user->id)->first();
        if ($mitra) {
            // Memproses konversi string kembali ke array jika bidang usaha diinput via teks terpisah koma
            $bidangInput = $request->bidang_perusahaan;
            if (str_contains($bidangInput, ',')) {
                $bidangArray = array_map('trim', explode(',', $bidangInput));
            } else {
                $bidangArray = [$bidangInput];
            }

            $mitra->update([
                'nama_lengkap' => $request->nama_lengkap,
                'no_telepon' => $request->no_telepon,
                'bidang_perusahaan' => $bidangArray, // Disimpan sebagai Array/JSON
                'deskripsi_perusahaan' => $request->deskripsi_perusahaan,
                'lokasi_perusahaan' => $request->lokasi_perusahaan,
            ]);
        }

        return redirect()->back()->with('success', 'Profil Akun dan Data Bisnis Kemitraan Berhasil Diperbarui!');
    }

    /**
     * Fitur Cetak Laporan (Export CSV)
     */
    public function exportListMitra(Request $request)
    {
        if (Auth::user()->usertype != 'admin') {
            abort(403);
        }

        $query = Mitra::where('status', 2)->withCount('mitraEventParticipations');

        if ($request->filled('tahun_cetak')) {
            $query->whereYear('created_at', $request->tahun_cetak);
        }

        if ($request->filled('bulan_cetak')) {
            $query->whereMonth('created_at', $request->bulan_cetak);
        }

        $mitras = $query->latest()->get();
        $filename = 'Laporan_Mitra_RumahBUMN_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($mitras) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'NAMA PERUSAHAAN', 'BIDANG USAHA', 'NAMA PIC', 'NOMOR TELEPON',
                'ALAMAT PERUSAHAAN', 'TOTAL KETERLIBATAN (SESI)', 'STATUS MONITORING',
                'RATING RATA-RATA', 'TANGGAL BERGABUNG'
            ]);

            foreach ($mitras as $m) {
                $bidangString = is_array($m->bidang_perusahaan) ? implode(', ', $m->bidang_perusahaan) : $m->bidang_perusahaan;

                fputcsv($file, [
                    $m->nama_perusahaan,
                    $bidangString,
                    $m->nama_lengkap,
                    $m->no_telepon,
                    $m->lokasi_perusahaan,
                    $m->mitra_event_participations_count,
                    $m->status_aktif ?? ($m->is_active ? 'Aktif' : 'Non-Aktif'),
                    number_format($m->average_rating ?? 0, 1),
                    $m->created_at->format('d-m-Y')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Menambahkan Mitra Secara Manual oleh Admin
     */
    public function storeManual(Request $request)
    {
        if (Auth::user()->usertype != 'admin') {
            abort(403);
        }

        $request->validate([
            'nama_lengkap' => 'required|string|regex:/^[a-zA-Z\s]+$/|max:255',
            'no_telepon' => 'required|regex:/^[0-9]+$/|max:20',
            'nama_perusahaan' => 'required|string|max:255',
            'bidang_perusahaan' => 'required|array',
            'lokasi_perusahaan' => 'required|string|max:255',
        ], [
            'nama_lengkap.required' => 'Kolom nama penanggung jawab wajib diisi.',
            'nama_lengkap.regex' => 'Nama penanggung jawab PIC dilarang menggunakan karakter angka atau simbol.',
            'no_telepon.required' => 'Kolom nomor telepon wajib diisi.',
            'no_telepon.regex' => 'Input nomor handphone wajib diisi dengan angka numerik murni.',
            'nama_perusahaan.required' => 'Kolom nama perusahaan wajib diisi.',
            'bidang_perusahaan.required' => 'Wajib memilih minimal satu kategori bidang usaha.',
            'lokasi_perusahaan.required' => 'Kolom lokasi perusahaan wajib diisi.',
        ]);

        $mitra = Mitra::create([
            'nama_lengkap' => $request->nama_lengkap,
            'no_telepon' => $request->no_telepon,
            'nama_perusahaan' => $request->nama_perusahaan,
            'bidang_perusahaan' => $request->bidang_perusahaan,
            'lokasi_perusahaan' => $request->lokasi_perusahaan,
            'deskripsi_perusahaan' => $request->deskripsi_perusahaan ?? 'Input Manual oleh Admin',
            'status' => 2,
            'status_aktif' => 'Aktif',
            'is_active' => true,
            'user_id' => Auth::id(),
        ]);

        HistoryMitra::create([
            'mitra_id' => $mitra->id,
            'action' => 'approved',
            'description' => 'Mitra ditambahkan secara manual oleh admin.',
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Mitra berhasil ditambahkan dan berstatus Aktif.');
    }

    public function detailIdentitas($id)
    {
        $mitra = Mitra::withCount('mitraEventParticipations')->findOrFail($id);
        return view('partnership.detail_identitas_mitra', compact('mitra'));
    }

    public function goDaftarMitra()
    {
        $user = Auth::user();
        $hasMitra = Mitra::where('user_id', $user->id)->exists();

        if ($hasMitra) {
            return redirect()->route('dashboard')->with('info', 'Anda sudah memiliki data pendaftaran.');
        }

        return view('partnership.daftar_mitra', compact('hasMitra'));
    }

    /**
     * Simpan Data Pendaftaran Mitra Baru Mandiri
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|regex:/^[a-zA-Z\s]+$/|max:255',
            'no_telepon' => 'required|regex:/^[0-9]+$/|max:20',
            'nama_perusahaan' => 'required|string|max:255',
            'bidang_perusahaan' => 'required|array',
            'lokasi_perusahaan' => 'required|string|max:255',
            'deskripsi_perusahaan' => 'required|string',
            'company_profile' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'surat_permohonan_audiensi' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ], [
            'nama_lengkap.required' => 'Kolom Nama Lengkap wajib diisi.',
            'nama_lengkap.regex' => 'Pendaftaran gagal! Nama penanggung jawab PIC hanya boleh diisi susunan huruf alfabet.',
            'no_telepon.required' => 'Kolom Nomor Telepon wajib diisi.',
            'no_telepon.regex' => 'Pendaftaran gagal! Input nomor telepon wajib diisi susunan angka numerik murni.',
            'nama_perusahaan.required' => 'Kolom Nama Perusahaan wajib diisi.',
            'bidang_perusahaan.required' => 'Wajib memilih minimal satu Bidang Usaha / Kategori Pelatihan.',
            'lokasi_perusahaan.required' => 'Kolom Alamat Perusahaan wajib diisi.',
            'deskripsi_perusahaan.required' => 'Kolom Deskripsi Usaha wajib diisi.',
            'company_profile.mimes' => 'Format file Company Profile harus berupa PDF, DOC, atau DOCX.',
            'company_profile.max' => 'Ukuran berkas Company Profile maksimal berukuran 5MB.',
            'surat_permohonan_audiensi.mimes' => 'Format file Surat Permohonan Audiensi harus berupa PDF, DOC, atau DOCX.',
            'surat_permohonan_audiensi.max' => 'Ukuran berkas Surat Permohonan Audiensi maksimal berukuran 5MB.',
        ]);

        $data = $request->only([
            'nama_lengkap', 'no_telepon', 'nama_perusahaan', 'bidang_perusahaan', 'lokasi_perusahaan', 'deskripsi_perusahaan'
        ]);

        $data['user_id'] = Auth::id();
        $data['status'] = 0;
        $data['status_aktif'] = 'Aktif';
        $data['is_active'] = true;

        if ($request->hasFile('company_profile')) {
            $fileName = time() . '_cp.' . $request->company_profile->extension();
            $request->company_profile->move(public_path('uploads/mitra'), $fileName);
            $data['company_profile'] = $fileName;
        }

        if ($request->hasFile('surat_permohonan_audiensi')) {
            $fileNameAudiensi = time() . '_audiensi.' . $request->surat_permohonan_audiensi->extension();
            $request->surat_permohonan_audiensi->move(public_path('uploads/mitra'), $fileNameAudiensi);
            $data['surat_permohonan_audiensi'] = $fileNameAudiensi;
        }

        $mitra = Mitra::create($data);

        HistoryMitra::create([
            'mitra_id' => $mitra->id,
            'action' => 'registered',
            'description' => 'Mitra baru melakukan pendaftaran.',
            'user_id' => Auth::id()
        ]);

        return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil dikirim!');
    }

    public function review($id)
    {
        $mitra = Mitra::findOrFail($id);
        $mitra->update(['status' => 1]);
        HistoryMitra::create(['mitra_id' => $mitra->id, 'action' => 'reviewed', 'description' => 'Peninjauan oleh admin.', 'user_id' => Auth::id()]);
        return redirect()->back()->with('success', 'Status: On Review.');
    }

    public function approve($id)
    {
        $mitra = Mitra::findOrFail($id);
        $mitra->update(['status' => 2, 'status_aktif' => 'Aktif', 'is_active' => true]);
        HistoryMitra::create(['mitra_id' => $mitra->id, 'action' => 'approved', 'description' => 'Pendaftaran disetujui.', 'user_id' => Auth::id()]);
        return redirect()->back()->with('success', 'Mitra diterima.');
    }

    public function reject($id)
    {
        $mitra = Mitra::findOrFail($id);
        $mitra->update(['status' => 3]);

        HistoryMitra::create([
            'mitra_id' => $mitra->id,
            'action' => 'rejected',
            'description' => 'Pendaftaran ditolak oleh manajemen admin Rumah BUMN Jakarta.',
            'user_id' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Pendaftaran Mitra ' . $mitra->nama_perusahaan . ' sukses ditolak.');
    }

    public function goKelolaPendaftaran()
    {
        if (Auth::user()->usertype != 'admin') return redirect('/');

        $mitras = Mitra::with('user')->whereIn('status', [0, 1])->orderBy('created_at', 'asc')->get();
        $rejectedMitras = Mitra::with('user')->where('status', 3)->orderBy('updated_at', 'desc')->get();

        return view('partnership.kelola_pendaftaran', compact('mitras', 'rejectedMitras'));
    }

    public function detail($id)
    {
        $mitra = Mitra::with('user')->findOrFail($id);
        $histories = HistoryMitra::with('user')->where('mitra_id', $id)->orderBy('created_at', 'desc')->get();
        return view('partnership.detail_mitra', compact('mitra', 'histories'));
    }

    public function goListMitra(Request $request)
    {
        return $this->index($request);
    }
}
