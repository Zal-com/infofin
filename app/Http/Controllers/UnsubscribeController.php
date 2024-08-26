<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\JWTService;
use Illuminate\Http\Request;

class UnsubscribeController extends Controller
{
    protected $jwtService;

    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function unsubscribe(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return view('unsubscribe')->with([
                'message' => "Le token n'a pas été fourni",
                'icon' => "heroicon-o-x-mark",
                "color" => "red"
            ]);
        }

        $userId = $this->jwtService->verifyUnsubscribeJWT($token);

        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $user->is_email_subscriber = 0;
                $user->save();
                return view('unsubscribe')->with([
                    'message' => "Vous avez été désabonné avec succès.",
                    'icon' => "heroicon-o-check",
                    "color" => "green"
                ]);
            }
            return view('unsubscribe')->with([
                'message' => "Utilisateur inexistant",
                'icon' => "heroicon-o-x-mark",
                "color" => "red"
            ]);
        }
        return view('unsubscribe')->with([
            'message' => "Lien de désabonnement invalide ou inexistant",
            'icon' => "heroicon-o-x-mark",
            "color" => "red"
        ]);
    }
}
