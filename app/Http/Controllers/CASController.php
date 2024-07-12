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

    public function handleCasCallback()
    {
        if (Cas::isAuthenticated()) {
            $attributes = Cas::getAttributes();
            //dd($attributes);
            $matricule = null;
            if (isset($attributes['ulbEmployeeNumber'])) {
                $matricule = Cas::getAttribute('ulbEmployeeNumber');
            } elseif (isset($attributes['ulbStudentNumber'])) {
                $matricule = Cas::getAttribute('ulbStudentNumber');
            }

            $ifUser = User::where('matricule', $matricule)->first();

            if ($ifUser) {
                Auth::login($ifUser);
                return redirect()->intended('/home');
            } else {
                //$user = User::firstOrCreate(['email' => $casUser]);

                //Auth::login($user);
                return redirect()->intended('/home');
            }
        }
        return redirect()->route('login');
    }
}
