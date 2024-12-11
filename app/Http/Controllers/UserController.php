<?php

namespace App\Http\Controllers;

use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function list()
    {
        $users = User::all();

        return view('welcome', [
            'users' => $users
        ]);
    }

    public function show($id)
    {
        if ($id != Auth::id()) return redirect()->route('home')->with('error', "Vous n'êtes pas autorisé à accéder à cette page");
        
        //Page de l'utilisateur
        $user = User::find(Auth::id());
        return view('users.show', $user);
    }

    public function index()
    {
        return view('users.index');
    }
}
