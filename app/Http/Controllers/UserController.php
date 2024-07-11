<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function list(){
        $users = User::all();

        return view('welcome', [
            'users' => $users
        ]);
    }

    public function show(){
        //Page de l'utilisateur
        $user = User::find(Auth::id());
        return view('users.show', $user);
    }

    public function index(){
        return view('users.index');
    }
}
