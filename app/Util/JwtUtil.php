<?php

declare(strict_types=1);

namespace App\Util;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtUtil
{
    public static function parseJwt(string $jwt)
    {
        try {
            $decoded = JWT::decode($jwt, new Key(env('JWT_SECRET'), 'HS256'));
            return (array)$decoded;
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            return false;
        }
    }
}
