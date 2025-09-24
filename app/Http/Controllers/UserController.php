<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
}
