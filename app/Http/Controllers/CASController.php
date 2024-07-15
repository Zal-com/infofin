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
                Auth::attempt([$ifUser->email, $ifUser->password]);
                return redirect()->intended('/');
            } else {
                //$user = User::firstOrCreate(['email' => $casUser]);
                dd($attributes);
                //Auth::login($user);
                return redirect()->intended('/');
            }
        }
        return redirect()->route('login');
    }
}
