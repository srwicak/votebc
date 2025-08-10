<?php

namespace App\Libraries;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JWT
{
    private $key;
    private $algo = 'HS256';

    public function __construct()
    {
        $this->key = getenv('app.key') ?: 'evoting_secret_key';
    }

    public function encode($payload, $exp = 3600)
    {
        $payload['iat'] = time();
        $payload['exp'] = time() + $exp;
        
        return FirebaseJWT::encode($payload, $this->key, $this->algo);
    }

    public function decode($token)
    {
        try {
            return (array) FirebaseJWT::decode($token, new Key($this->key, $this->algo));
        } catch (\Exception $e) {
            return false;
        }
    }
}