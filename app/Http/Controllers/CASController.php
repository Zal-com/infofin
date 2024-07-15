<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
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
                Auth::attempt([$ifUser->email, $ifUser->password]);
                return redirect()->intended('/');
            } else {
                dd($attributes);
                //$user = User::firstOrCreate();
                //dd($attributes);
                Auth::attempt([$casUser, $casUser->password, $casUser->password]);
                return redirect()->intended('/');
            }
        }
        return redirect()->route('login');
    }

    public function logout(Request $request)
    {

        if (Cas::isAuthenticated()) {
            Cas::logout();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();


        return redirect()->route('login');
    }
}
