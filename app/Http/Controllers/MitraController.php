<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\HistoryMitra;
use App\Models\MitraEventParticipation;
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
    public function goListMitra(Request $request)
    {
        $user = Auth::user();

        // Proteksi Akses Admin
        if ($user->usertype != 'admin') {
            return redirect('/');
        }

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

        // Logic Sorting Khusus
        if ($request->sort == 'keaktifan_low') {
            // Sort berdasarkan jumlah partisipasi tersedikit (Rekomendasi Non-Aktif)
            $query->orderBy('mitra_event_participations_count', 'asc');
        } elseif ($request->sort == 'rating_high') {
            // Sort berdasarkan rating rata-rata tertinggi
            $query->orderBy('average_rating', 'desc');
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
                    // Jika tidak mencapai target 3 sesi di tahun sebelumnya
                    $mitra->status_aktif = 'Non-Aktif';
                    $mitra->is_active = false;
                } else {
                    // Berhasil memenuhi syarat keaktifan tahun lalu
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
     * Fitur Cetak Laporan (Export CSV)
     * Mendukung Filter per Tahun dan per Bulan secara dinamis dari tabel silabus.
     */
    public function exportListMitra(Request $request)
    {
        if (Auth::user()->usertype != 'admin') {
            abort(403);
        }

        $query = Mitra::where('status', 2)->withCount('mitraEventParticipations');

        // Filter Cetak Berdasarkan Tahun (created_at)
        if ($request->filled('tahun_cetak')) {
            $query->whereYear('created_at', $request->tahun_cetak);
        }

        // Filter Cetak Berdasarkan Bulan (created_at)
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

            // Header Baris Pertama CSV
            fputcsv($file, [
                'NAMA PERUSAHAAN',
                'BIDANG USAHA',
                'NAMA PIC',
                'NOMOR TELEPON',
                'ALAMAT PERUSAHAAN',
                'TOTAL KETERLIBATAN (SESI)',
                'STATUS MONITORING',
                'RATING RATA-RATA',
                'TANGGAL BERGABUNG'
            ]);

            foreach ($mitras as $m) {
                // SINKRONISASI CSV EXPORT: Konversi data array bidang_perusahaan menjadi string terpisah koma
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
     * FITUR REVISI DOSEN: Menerima masukan multi-checkbox kategori berupa array
     */
    public function storeManual(Request $request)
    {
        if (Auth::user()->usertype != 'admin') {
            abort(403);
        }

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
            'nama_perusahaan' => 'required|string|max:255',
            'bidang_perusahaan' => 'required|array', // Diubah menjadi array wajib
            'lokasi_perusahaan' => 'required|string|max:255',
        ]);

        $mitra = Mitra::create([
            'nama_lengkap' => $request->nama_lengkap,
            'no_telepon' => $request->no_telepon,
            'nama_perusahaan' => $request->nama_perusahaan,
            'bidang_perusahaan' => $request->bidang_perusahaan, // Otomatis dicast ke JSON oleh Model
            'lokasi_perusahaan' => $request->lokasi_perusahaan,
            'deskripsi_perusahaan' => $request->deskripsi_perusahaan ?? 'Input Manual oleh Admin',
            'status' => 2,
            'status_aktif' => 'Aktif',
            'is_active' => true, // Default Mitra baru adalah Aktif (Grace Period)
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

    /**
     * Menampilkan Detail Identitas Mitra dengan hitungan partisipasi realtime
     */
    public function detailIdentitas($id)
    {
        $mitra = Mitra::withCount('mitraEventParticipations')->findOrFail($id);

        return view('partnership.detail_identitas_mitra', compact('mitra'));
    }

    /**
     * Proses Alur Pendaftaran Mandiri oleh Mitra
     */
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
     * Simpan Data Pendaftaran Mitra Baru (Status Awal Aktif)
     * FITUR REVISI DOSEN: Mengubah validasi bidang_perusahaan agar mendukung input array checkbox
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
            'nama_perusahaan' => 'required|string|max:255',
            'bidang_perusahaan' => 'required|array', // Diubah menjadi array wajib
            'lokasi_perusahaan' => 'required|string|max:255',
            'deskripsi_perusahaan' => 'required|string',
            'company_profile' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $data = $request->only([
            'nama_lengkap',
            'no_telepon',
            'nama_perusahaan',
            'bidang_perusahaan', // Array checkbox tersimpan aman
            'lokasi_perusahaan',
            'deskripsi_perusahaan'
        ]);

        $data['user_id'] = Auth::id();
        $data['status'] = 0; // Waiting Review
        $data['status_aktif'] = 'Aktif';
        $data['is_active'] = true; // Status Aktif awal (Grace Period)

        if ($request->hasFile('company_profile')) {
            $fileName = time() . '_cp.' . $request->company_profile->extension();
            $request->company_profile->move(public_path('uploads/mitra'), $fileName);
            $data['company_profile'] = $fileName;
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

    // --- ALUR VERIFIKASI ADMIN ---

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
        HistoryMitra::create(['mitra_id' => $mitra->id, 'action' => 'rejected', 'description' => 'Pendaftaran ditolak.', 'user_id' => Auth::id()]);
        return redirect()->back()->with('success', 'Mitra ditolak.');
    }

    public function goKelolaPendaftaran()
    {
        if (Auth::user()->usertype != 'admin') return redirect('/');
        $mitras = Mitra::with('user')->whereIn('status', [0, 1])->orderBy('created_at', 'asc')->get();
        return view('partnership.kelola_pendaftaran', compact('mitras'));
    }

    public function detail($id)
    {
        $mitra = Mitra::with('user')->findOrFail($id);
        $histories = HistoryMitra::with('user')->where('mitra_id', $id)->orderBy('created_at', 'desc')->get();
        return view('partnership.detail_mitra', compact('mitra', 'histories'));
    }
}
