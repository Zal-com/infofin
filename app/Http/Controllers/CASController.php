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

    public function handleCasCallback(Request $request)
    {
        if (Cas::isAuthenticated()) {
            $attributes = Cas::getAttributes();
            $uid = $attributes["uid"];

            $user = User::where("uid", $uid)->first();

            if (!$user) {
                $userDetails = [
                    "email" => $attributes["mail"],
                    "first_name" => $attributes["givenName"],
                    "last_name" => $attributes["sn"],
                    "uid" => $attributes["uid"],
                ];

                $request->session()->flash("userDetails", $userDetails);
                return redirect()->route("login.first");
            } else {
                Auth::login($user);
                return redirect()->route('projects.index');
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

    public function policy_create_user(Request $request){
        $usersDetails = session("userDetails");
        dd($usersDetails);
        return view();
    }
}
