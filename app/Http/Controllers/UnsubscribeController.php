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
            return response()->json(['message' => 'Token de désabonnement non fourni.'], 400);
        }

        $userId = $this->jwtService->verifyUnsubscribeJWT($token);

        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $user->is_email_subscriber = 0;
                $user->save();
                return response()->json(['message' => 'Vous êtes désabonné avec succès.']);
            }
            return response()->json(['message' => 'User inexistant.'], 400);
        }
        return response()->json(['message' => 'Lien de désabonnement invalide ou expiré.'], 400);
    }
}
