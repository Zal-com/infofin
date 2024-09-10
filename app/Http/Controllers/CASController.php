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
         * array:17 [▼ // app/Http/Controllers/CASController.php:27
         *  "clientIpAddress" => "10.88.2.205"
         *  "isFromNewLogin" => "true"
         *  "mail" => "axel.hoffmann@ulb.be"
         *  "authenticationDate" => "2024-09-10T16:27:41.246108Z"
         *  "eduPersonAffiliation" => "employee"
         *  "givenName" => "Axel"
         *  "successfulAuthenticationHandlers" => "LdapAuthenticationHandler"
         *  "userAgent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:130.0) Gecko/20100101 Firefox/130.0"
         *  "ulbEmployeeNumber" => "50023185"
         *  "credentialType" => "UsernamePasswordCredential"
         *  "samlAuthenticationStatementAuthMethod" => "urn:oasis:names:tc:SAML:1.0:am:password"
         *  "uid" => "ahof0006"
         *  "eduPersonOrgUnitDN" => "ou=dpt rech stari,ou=departement recherche,ou=administration generale,ou=universite libre de bruxelles,ou=orgs,dc=ulb,dc=be"
         *  "authenticationMethod" => "LdapAuthenticationHandler"
         *  "serverIpAddress" => "172.31.8.125"
         *  "longTermAuthenticationRequestTokenUsed" => "false"
         *  "sn" => "Hoffmann"
         *  ]
         */
        if (Cas::isAuthenticated()) {
            $attributes = Cas::getAttributes();
            $matricule = null;
            if (isset($attributes['ulbEmployeeNumber'])) {
                $matricule = Cas::getAttribute('ulbEmployeeNumber');
            } elseif (isset($attributes['ulbStudentNumber'])) {
                $matricule = Cas::getAttribute('ulbStudentNumber');
            }

            $redirectUrl = session()->pull('url.intended', '/');

            $ifUser = User::where('matricule', $matricule)->first();

            if ($ifUser) {
                Auth::login($ifUser);
                return redirect()->intended($redirectUrl);
            } else {
                $user = new User([
                    'matricule' => $matricule,
                    'first_name' => $attributes['givenName'],
                    'last_name' => $attributes['sn'],
                    'password' => '',
                    'email' => $attributes['mail']]);
                $user->save();
                Auth::login($user);
                return redirect()->intended($redirectUrl);
            }
        }
        return redirect()->route('login');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if (Cas::isAuthenticated()) {
            Cas::logout();
        }

        return redirect()->route('login');
    }
}
