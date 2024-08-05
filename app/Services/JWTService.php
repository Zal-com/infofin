<?php

namespace App\Services;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService
{
    protected $secretKey;

    public function __construct()
    {
        $this->secretKey = env('JWT_SECRET');
    }

    public function generateUnsubscribeJWT($userId)
    {
        $payload = [
            'user_id' => $userId,
            'exp' => time() + (7 * 24 * 60 * 60) // Expiration dans 7 jours
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function verifyUnsubscribeJWT($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return $decoded->user_id;
        } catch (Exception $e) {
            // Gérer les erreurs de décodage (token expiré, invalide, etc.)
            return null;
        }
    }
}
