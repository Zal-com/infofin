<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function list(){
        $users = User::all();

        return view('welcome', [
            'users' => $users
        ]);
    }

    public function index(){
        return view('users.index');
    }
}
