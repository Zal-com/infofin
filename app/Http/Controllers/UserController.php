<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function list(){
        $users = User::all();

        return view('welcome', [
            'users' => $users
        ]);
    }
}
