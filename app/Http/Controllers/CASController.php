<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Subfission\Cas\Facades\Cas;


class CASController extends Controller
{
    public function redirectToCas(Request $request)
    {
        if (Cas::isAuthenticated()) {
            $attributes = Cas::getAttributes();
            $uid = $attributes["uid"];

            $user = User::where("uid", $uid)->first();

            if (!$user) {
                $userDetails = [
                    "email" => $attributes["mail"] ?? $attributes["ulbContactMail"],
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
        return Cas::authenticate();
    }

    public function handleCasCallback(Request $request)
    {
        if (Cas::isAuthenticated()) {
            $attributes = Cas::getAttributes();
            foreach ($attributes as $attribute => $value) {
                dump($attribute);
            }
            die();
            $uid = $attributes["uid"];

            $user = User::where("uid", $uid)->first();

            if (!$user) {
                $userDetails = [
                    "email" => $attributes["mail"] ?? $attributes["ulbContactMail"],
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

        return redirect()->route('projects.index');
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

    public function policy_create_user()
    {
        $userDetails = session("userDetails");
        if (!$userDetails) {
            return redirect()->route('projects.index'); // redirect to login if no details found
        }
        return view('auth.policy-create-user', ['userDetails' => $userDetails]);
    }
}
