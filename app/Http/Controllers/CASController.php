<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
        /*
         * Authentification CAS :
         * Si l'utilisateur existe dans la base de données avec son matricule, il sera automatiquement connecté avec ses informations
         * Si l'utilisateur n'existe pas dans la base de données, il sera connecté avec son matricule, via le CAS
         */
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
                return redirect()->intended('/');
            } else {
                $user = new User([
                    'matricule' => $matricule,
                    'first_name' => $attributes['givenName'],
                    'last_name' => $attributes['sn'],
                    'password' => '',
                    'email' => $attributes['mail']]);
                $user->save();
                Auth::login($user);
                return redirect()->intended('/');
            }
        }
        return redirect()->route('login');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        if (Cas::isAuthenticated()) {
            Cas::logout();
        }

        $request->session()->invalidate();

        $request->session()->regenerateToken();


        return redirect()->route('login');
    }
}
