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
            'email'=>'required|email',
            'password'=>'required',
        ]);

        $user = User::Create($validation);

        Auth::login($user);

        return redirect()->route('login');

    }

    public function goDashboard(){
        $user = Auth::user();
        return view('partnership.dashboard', compact('user'));
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
}
