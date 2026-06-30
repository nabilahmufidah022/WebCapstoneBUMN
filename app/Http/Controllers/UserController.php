<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function login()
    {
        return view('login/login');
    }

    public function signup()
    {
        return view('login/registration');
    }

    public function logincheck(Request $request)
    {
        $credential = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credential)) {
            return redirect()->route('dashboard');
        }

        return back()
            ->withErrors(['email' => 'Invalid credentials'])
            ->withInput();
    }

    public function registercheck(Request $request)
    {
        $validation = $request->validate([
            // Validasi nama: wajib susunan huruf dan spasi saja
            'name' => 'required|regex:/^[a-zA-Z\s]+$/',

            // Validasi email: wajib @gmail.com
            'email' => 'required|email|ends_with:@gmail.com|unique:users,email',

            'password' => 'required|min:8',
        ], [
            'name.regex' => 'Format nama tidak sah! Nama lengkap hanya boleh berupa susunan huruf dan spasi tanpa angka.',
            'email.ends_with' => 'Pendaftaran ditolak! Akun kemitraan wajib menggunakan domain email resmi @gmail.com.',
        ]);

        $validation['password'] = Hash::make($validation['password']);

        $user = User::create($validation);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function goDashboard()
    {
        $user = Auth::user();

        if ($user->usertype === 'admin') {

            // Dashboard Admin
            $totalMitra = \App\Models\Mitra::count();
            $totalParticipations = \App\Models\MitraEventParticipation::count();

            $mitraPieData = [
                'labels' => ['Total Mitra Terdaftar'],
                'data' => [$totalMitra],
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
        }

        // ==========================================
        // Dashboard Mitra
        // ==========================================

        $mitra = $user->mitra;

        $data = [
            'total_kerjasama' => 0,
            'agenda_mendatang' => 0,
            'perlu_evaluasi' => 0,
        ];

        if ($mitra) {

            // Histori Kerjasama
            $participations = $mitra->mitraEventParticipations()
                ->latest()
                ->take(5)
                ->get();

            // Statistik
            $data['total_kerjasama'] = $mitra
                ->mitraEventParticipations()
                ->count();

            $data['agenda_mendatang'] = $mitra
                ->mitraEventParticipations()
                ->where('status', 'Akan Datang')
                ->count();

            $data['perlu_evaluasi'] = $mitra
                ->mitraEventParticipations()
                ->where('status', 'Selesai')
                ->whereNull('rating_mitra')
                ->count();

            // Notifikasi
            $notifications = \App\Models\HistoryMitra::where('mitra_id', $mitra->id)
                ->where('action', 'reviewed')
                ->latest()
                ->take(5)
                ->get();

        } else {

            $participations = collect();
            $notifications = collect();
        }

        return view('partnership.dashboard', compact(
            'user',
            'mitra',
            'participations',
            'data',
            'notifications'
        ));
    }
        /**
     * INTEGRASI FITUR: Halaman Profil Pengguna
     * Memuat data relasi profil bisnis mitra secara realtime.
     */
    public function profile(Request $request)
    {
        $currentUser = Auth::user();

        // Jalur Admin
        if ($currentUser->usertype === 'admin' && $request->filled('user_id')) {

            $user = User::findOrFail($request->user_id);
            $mitra = $user->mitra;

        } else {

            // Jalur Mandiri
            $user = $currentUser;
            $mitra = null;

            if ($user->usertype === 'user' || $user->usertype === 'mitra') {
                $mitra = $user->mitra;
            }
        }

        return view('partnership.profile', compact('user', 'mitra'));
    }

    public function settings()
    {
        $user = Auth::user();

        return view('partnership.settings', compact('user'));
    }

    /**
     * Update Profil
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $namaLama = $user->name;
        $emailLama = $user->email;

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->hasFile('profile_image')) {

            $imageName = time() . '.' . $request->profile_image->extension();

            $request->profile_image->move(
                public_path('img'),
                $imageName
            );

            $data['profile_image'] = $imageName;
        }

        $user->update($data);

        // Sinkronisasi data PIC
        if (
            $user->usertype !== 'admin' &&
            $namaLama !== $request->name &&
            $user->mitra
        ) {

            $user->mitra->update([
                'nama_lengkap' => $request->name,
            ]);

            \App\Models\HistoryMitra::create([
                'mitra_id' => $user->mitra->id,
                'action' => 'reviewed',
                'description' =>
                    "NOTIFIKASI HANDOVER: Pergantian PIC dilakukan secara mandiri dari {$namaLama} ({$emailLama}) menjadi {$request->name} ({$request->email}).",
                'user_id' => Auth::id(),
            ]);
        }

        return redirect()
            ->route('profile')
            ->with('success', 'Profile data updated successfully.');
    }

    /**
     * Handover PIC
     */
    public function handoverPIC(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'new_pic_name' => 'required|string|max:255',
            'new_pic_email' => 'required|email|unique:users,email,' . $user->id,
            'new_pic_phone' => 'required|string|max:20',
        ]);

        $namaLama = $user->name;
        $emailLama = $user->email;

        // Update akun user
        $user->update([
            'name' => $request->new_pic_name,
            'email' => $request->new_pic_email,
        ]);

        // Update data mitra
        if ($user->mitra) {

            $user->mitra->update([
                'nama_lengkap' => $request->new_pic_name,
                'no_telepon' => $request->new_pic_phone,
            ]);

            \App\Models\HistoryMitra::create([
                'mitra_id' => $user->mitra->id,
                'action' => 'reviewed',
                'description' =>
                    "NOTIFIKASI HANDOVER RESMI: Alih kepemilikan akses akun penanggung jawab sukses diselesaikan dari {$namaLama} ({$emailLama}) kepada PIC pengganti: {$request->new_pic_name} ({$request->new_pic_email}) dengan No. WA: {$request->new_pic_phone}.",
                'user_id' => Auth::id(),
            ]);
        }

        return redirect()
            ->route('profile')
            ->with(
                'success',
                'Proses Handover PIC berhasil dilakukan. Data akun penanggung jawab telah dimutasi.'
            );
    }
        /**
     * Mengubah Password User
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect'
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Audit Log
        if ($user->mitra) {

            \App\Models\HistoryMitra::create([
                'mitra_id' => $user->mitra->id,
                'action' => 'reviewed',
                'description' => "Pemberitahuan Keamanan: Anda telah berhasil memperbarui kata sandi akses akun pada tanggal " .
                    now()->format('d-m-Y H:i') . " WIB.",
                'user_id' => Auth::id(),
            ]);
        }

        return back()->with(
            'success',
            'Password changed successfully'
        );
    }

    /**
     * Tahap 1 - Verifikasi Email Reset Password
     */
    public function checkEmailForReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|ends_with:@gmail.com'
        ], [
            'email.required' => 'Gagal memproses! Kolom input email tidak boleh dibiarkan kosong.',
            'email.email' => 'Format penulisan tidak sah! Pastikan menyertakan karakter @ dengan benar.',
            'email.ends_with' => 'Akses ditolak! Sistem kemitraan wajib menggunakan domain email resmi @gmail.com.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()
                ->withErrors([
                    'email' => 'Verifikasi Gagal! Alamat email tersebut belum terdaftar di dalam sistem Rumah BUMN.'
                ])
                ->withInput();
        }

        return redirect()
            ->route('password.reset.page')
            ->with('reset_email', $request->email);
    }

    /**
     * Tahap 2 - Reset Password
     */
    public function executeInAppReset(Request $request)
    {
        if ($request->filled('email')) {
            session()->flash('reset_email', $request->email);
        }

        $request->flash();

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Gagal menyimpan! Kolom input password baru tidak boleh kosong.',
            'password.min' => 'Keamanan lemah! Kata sandi baru wajib bertumpu minimal pada 8 karakter.',
            'password.confirmed' => 'Sinkronisasi gagal! Ketikan pada kolom konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user = User::where('email', $request->email)->first();

        $user->password = Hash::make($request->password);
        $user->save();

        // Audit Log
        if ($user->mitra) {

            \App\Models\HistoryMitra::create([
                'mitra_id' => $user->mitra->id,
                'action' => 'reviewed',
                'description' => "Pemberitahuan Keamanan: Pemulihan kata sandi akun via web diselesaikan sukses pada " .
                    now()->format('d-m-Y H:i') . " WIB.",
                'user_id' => $user->id,
            ]);
        }

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Kata sandi Anda berhasil diperbarui di database! Silakan mencoba login kembali menggunakan password baru.'
            );
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
        /**
     * Account Management
     */
    public function account()
    {
        $users = User::all();

        return view('admin/account', compact('users'));
    }

    public function createAccount()
    {
        return view('admin/create_account');
    }

    public function storeAccount(Request $request)
    {
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
            return response()->json([
                'success' => true,
                'message' => 'Account created successfully.',
            ]);
        }

        return redirect()
            ->route('account')
            ->with('success', 'Account created successfully.');
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
            return response()->json([
                'success' => true,
                'message' => 'Account updated successfully.',
            ]);
        }

        return redirect()
            ->route('account')
            ->with('success', 'Account updated successfully.');
    }

    public function deleteAccount($id)
    {
        $user = User::findOrFail($id);

        $user->is_active = !$user->is_active;
        $user->save();

        $message = $user->is_active
            ? 'Account activated successfully.'
            : 'Account deactivated successfully.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_active' => $user->is_active,
        ]);
    }

    public function destroyPermanently($id)
    {
        $user = User::findOrFail($id);

        if ($user->mitra) {
            $user->mitra()->delete();
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted permanently.',
        ]);
    }

    public function destroySelf(Request $request)
    {
        $user = Auth::user();

        if ($user->mitra) {
            $user->mitra()->delete();
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Your account has been deleted permanently.'
            );
    }
}