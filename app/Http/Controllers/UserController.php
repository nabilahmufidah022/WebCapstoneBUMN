<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

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
            $pendingMitra = \App\Models\Mitra::where('status', 'pending')->count();
            $approvedMitra = \App\Models\Mitra::where('status', 'approved')->count();
            $totalParticipations = \App\Models\MitraEventParticipation::count();
            $recentParticipations = \App\Models\MitraEventParticipation::with('mitra')->latest()->take(5)->get();

            return view('partnership.dashboard', compact(
                'user',
                'totalMitra',
                'pendingMitra',
                'approvedMitra',
                'totalParticipations',
                'recentParticipations'
            ));
        } else {
            // User dashboard data
            $mitra = $user->mitra; // Assuming relationship exists
            $participations = $mitra ? $mitra->mitraEventParticipations()->latest()->take(5)->get() : collect();

            return view('partnership.dashboard', compact('user', 'mitra', 'participations'));
        }
    }

    public function profile(){
        $user = Auth::user();
        return view('partnership.profile', compact('user'));
    }

    public function settings(){
        $user = Auth::user();
        return view('partnership.settings', compact('user'));
    }

    public function updateProfile(Request $request){
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->hasFile('profile_image')) {
            $imageName = time() . '.' . $request->profile_image->extension();
            $request->profile_image->move(public_path('img'), $imageName);
            $data['profile_image'] = $imageName;
        }

        $user->update($data);

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
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

        User::create([
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

        // Toggle active state instead of deleting
        $user->is_active = !$user->is_active;
        $user->save();

        $message = $user->is_active ? 'Account activated successfully.' : 'Account deactivated successfully.';

        return response()->json(['success' => true, 'message' => $message, 'is_active' => $user->is_active]);
    }

    public function destroyPermanently($id)
    {
        $user = User::findOrFail($id);
        
        // Delete related mitra data if exists to prevent foreign key issues 
        // (Assuming ON DELETE CASCADE is not set in DB, if it is, this is just extra safety)
        if ($user->mitra) {
            $user->mitra()->delete();
        }
        
        $user->delete();

        return response()->json(['success' => true, 'message' => 'Account deleted permanently.']);
    }

    public function destroySelf(Request $request) {
        $user = Auth::user();

        // Delete related mitra data
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
