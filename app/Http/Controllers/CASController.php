<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Subfission\Cas\Facades\Cas;


class CASController extends Controller
{
    public function redirectToCas()
    {
        return Cas::authenticate();
    }

    public function handleCasCallback(){
        if(Cas::isAuthenticated()){
            dd(Cas::getAttributes());
            $casUser = Cas::getCurrentUser();
            dd($casUser);
            $user = User::firstOrCreate(['email' => $casUser]);

            Auth::login($user);
            return redirect()->intended('/home');
        }
        return redirect()->route('login');
    }
}
