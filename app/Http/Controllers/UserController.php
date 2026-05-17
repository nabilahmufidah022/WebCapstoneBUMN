<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function login(){
        return view('login/login');
    }

    public function signup(){
        return view('login/registration');
    }

    public function logincheck (Request $request) {
        $credential = $request->validate([
            'email'=>'required|email',
            'password' => 'required',
        ]);

        if(Auth:: attempt($credential)){
            return redirect()->route('dashboard');
        } else {
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }
    }

    public function registercheck (Request $request){

        $validation = $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:8',
        ]);

        $validation['password'] = Hash::make($validation['password']);

        $user = User::create($validation);

        Auth::login($user);

        return redirect()->route('dashboard');

    }

    public function goDashboard(){
        $user = Auth::user();

        if ($user->usertype === 'admin') {
            // Admin dashboard data
            $totalMitra = \App\Models\Mitra::count();
            $totalParticipations = \App\Models\MitraEventParticipation::count();

            $mitraPieData = [
                'labels' => ['Total Mitra Terdaftar'],
                'data' => [$totalMitra]
            ];

            $agendaTrend = \App\Models\MitraEventParticipation::select(
                    DB::raw('MONTHNAME(tanggal_pelatihan) as month'),
                    DB::raw('count(*) as total')
                )
                ->groupBy('month')
                ->orderBy('tanggal_pelatihan', 'asc')
                ->get();

            $recentParticipations = \App\Models\MitraEventParticipation::with('mitra')
                ->latest()
                ->take(5)
                ->get();

            return view('partnership.dashboard', compact(
                'user',
                'totalMitra',
                'totalParticipations',
                'mitraPieData',
                'agendaTrend',
                'recentParticipations'
            ));
        } else {
            // ==========================================
            // DASHBOARD MITRA
            // ==========================================
            $mitra = $user->mitra;
            $data = [
                'total_kerjasama' => 0,
                'agenda_mendatang' => 0,
                'perlu_evaluasi' => 0
            ];

            if ($mitra) {
                // 1. Ambil Histori Kerjasama Terbaru (Limit 5)
                $participations = $mitra->mitraEventParticipations()->latest()->take(5)->get();

                // 2. Hitung Statistik untuk Widget Dashboard Mitra
                $data['total_kerjasama'] = $mitra->mitraEventParticipations()->count();

                $data['agenda_mendatang'] = $mitra->mitraEventParticipations()
                    ->where('status', 'Akan Datang')
                    ->count();

                // SINKRONISASI: Cek kolom rating_mitra agar widget "Perlu Evaluasi" berkurang setelah feedback dikirim
                $data['perlu_evaluasi'] = $mitra->mitraEventParticipations()
                    ->where('status', 'Selesai')
                    ->whereNull('rating_mitra')
                    ->count();
            } else {
                $participations = collect();
            }

            return view('partnership.dashboard', compact('user', 'mitra', 'participations', 'data'));
        }
    }

    /**
     * INTEGRASI FITUR: Halaman Profil Pengguna
     * Memuat data relasi profil bisnis mitra secara realtime.
     */
    public function profile(Request $request){
        // Menangkap objek user yang sedang melakukan session login saat ini
        $currentUser = Auth::user();

        // JALUR ADMIN BYPASS: Jika peran login adalah admin dan terdapat parameter user_id target pada URL
        if ($currentUser->usertype === 'admin' && $request->filled('user_id')) {

            // Ambil data user target yang akunnya ingin dimutasi atau dibypass oleh admin
            $user = User::findOrFail($request->user_id);
            $mitra = $user->mitra;

        } else {

            // JALUR MANDIRI: Jika diakses secara personal oleh user/mitra itu sendiri dari menu profile asli
            $user = $currentUser;
            $mitra = null;
            if ($user->usertype === 'user' || $user->usertype === 'mitra') {
                $mitra = $user->mitra;
            }

        }

        return view('partnership.profile', compact('user', 'mitra'));
    }

    public function settings(){
        $user = Auth::user();
        return view('partnership.settings', compact('user'));
    }

    /**
     * SINKRONISASI ANTAR SERVER & INTEGRASI FITUR: HANDOVER PIC AUTOMATION (DUAL-CHANNEL)
     * Jalur A (Mandiri Mitra): Mendeteksi pembaruan profil / transisi PIC biasa secara seamless.
     */
    public function updateProfile(Request $request){
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Tangkap nama dan email lama sebelum disimpan untuk keperluan audit log handover
        $namaLama = $user->name;
        $emailLama = $user->email;

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->hasFile('profile_image')) {
            $imageName = time() . '.' . $request->profile_image->extension();
            $request->profile_image->move(public_path('img'), $imageName);
            $data['profile_image'] = $imageName;
        }

        // Lakukan pembaruan data dasar user account (Berlaku untuk Admin maupun Mitra)
        $user->update($data);

        // [JALUR A] JALUR MANDIRI SEAMLESS: Hanya berjalan untuk user/mitra biasa (Bukan Admin) jika namanya diubah
        if ($user->usertype !== 'admin' && $namaLama !== $request->name && $user->mitra) {

            // Perbarui kolom penanggung jawab / nama lengkap di tabel mitra agar sinkron otomatis
            $user->mitra->update([
                'nama_lengkap' => $request->name
            ]);

            // Menggunakan model HistoryMitra untuk mencatat log mutasi pergantian antar PIC secara mandiri
            \App\Models\HistoryMitra::create([
                'mitra_id' => $user->mitra->id,
                'action' => 'reviewed',
                'description' => "NOTIFIKASI HANDOVER: Pergantian PIC dilakukan secara mandiri dari " . $namaLama . " (" . $emailLama . ") menjadi " . $request->name . " (" . $request->email . ").",
                'user_id' => Auth::id()
            ]);
        }

        return redirect()->route('profile')->with('success', 'Profile data updated successfully.');
    }

    /**
     * FITUR JALUR KHUSUS: HANDOVER PIC AUTOMATION VIA MODAL
     * Memproses alih penanggung jawab akun mitra ke entitas baru secara resmi dan terstruktur.
     */
    public function handoverPIC(Request $request) {
        $user = Auth::user();

        // Validasi input khusus yang datang dari modal form handover (Nama, Email, dan No WA Baru)
        $request->validate([
            'new_pic_name'  => 'required|string|max:255',
            'new_pic_email' => 'required|email|unique:users,email,' . $user->id,
            'new_pic_phone' => 'required|string|max:20',
        ]);

        // Rekam data lama untuk keperluan catatan log riwayat (Audit Trail)
        $namaLama = $user->name;
        $emailLama = $user->email;

        // 1. Eksekusi pembaruan data kredensial akun user yang aktif saat ini
        $user->update([
            'name'  => $request->new_pic_name,
            'email' => $request->new_pic_email,
        ]);

        // 2. Jika akun terikat dengan data kemitraan Rumah BUMN, sinkronisasikan nama PIC utamanya beserta Nomor Teleponnya
        if ($user->mitra) {
            $user->mitra->update([
                'nama_lengkap' => $request->new_pic_name,
                'no_telepon'   => $request->new_pic_phone
            ]);

            // 3. Masukkan jejak rekam mutasi ke dalam tabel log HistoryMitra
            \App\Models\HistoryMitra::create([
                'mitra_id'    => $user->mitra->id,
                'action'      => 'reviewed',
                'description' => "NOTIFIKASI HANDOVER RESMI: Alih kepemilikan akses akun penanggung jawab sukses diselesaikan dari " . $namaLama . " (" . $emailLama . ") kepada PIC pengganti: " . $request->new_pic_name . " (" . $request->new_pic_email . ") dengan No. WA: " . $request->new_pic_phone . ".",
                'user_id'     => Auth::id()
            ]);
        }

        return redirect()->route('profile')->with('success', 'Proses Handover PIC berhasil dilakukan. Data akun penanggung jawab telah dimutasi.');
    }

    public function changePassword(Request $request){
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password changed successfully');
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function account(){
        $users = User::all();
        return view('admin/account', compact('users'));
    }

    public function createAccount(){
        return view('admin/create_account');
    }

    public function storeAccount(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'usertype' => 'required|in:user,admin',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'usertype' => $request->usertype,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Account created successfully.']);
        }

        return redirect()->route('account')->with('success', 'Account created successfully.');
    }

    public function editAccount($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function updateAccount(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'usertype' => 'required|in:user,admin',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'usertype' => $request->usertype,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Account updated successfully.']);
        }

        return redirect()->route('account')->with('success', 'Account updated successfully.');
    }

    public function deleteAccount($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        $message = $user->is_active ? 'Account activated successfully.' : 'Account deactivated successfully.';

        return response()->json(['success' => true, 'message' => $message, 'is_active' => $user->is_active]);
    }

    public function destroyPermanently($id)
    {
        $user = User::findOrFail($id);

        if ($user->mitra) {
            $user->mitra()->delete();
        }

        $user->delete();

        return response()->json(['success' => true, 'message' => 'Account deleted permanently.']);
    }

    public function destroySelf(Request $request) {
        $user = Auth::user();

        if ($user->mitra) {
            $user->mitra()->delete();
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Your account has been deleted permanently.');
    }
}
