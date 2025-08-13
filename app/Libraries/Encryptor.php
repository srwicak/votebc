<?php

namespace App\Libraries;

class Encryptor
{
    protected $encrypter;
    
    public function __construct()
    {
        $this->encrypter = \Config\Services::encrypter();
    }
    
    /**
     * Encrypt data
     *
     * @param mixed $data Data to encrypt
     * @return string Encrypted data
     */
    public function encrypt($data)
    {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        // Pastikan data bertipe string
        $data = (string) $data;
        return base64_encode($this->encrypter->encrypt($data));
    }
    
    /**
     * Decrypt data
     *
     * @param string $data Encrypted data
     * @param bool $asJson Whether to decode as JSON
     * @return mixed Decrypted data
     */
    public function decrypt($data, $asJson = false)
    {
        $decrypted = $this->encrypter->decrypt(base64_decode($data));
        
        if ($asJson) {
            return json_decode($decrypted, true);
        }
        
        return $decrypted;
    }
    
    /**
     * Generate a random key
     *
     * @param int $length Key length
     * @return string Random key
     */
    public function generateKey($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }
    
    /**
     * Hash data using SHA-256
     *
     * @param mixed $data Data to hash
     * @return string Hashed data
     */
    public function hash($data)
    {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        
        return hash('sha256', $data);
    }
    
    /**
     * Verify hash
     *
     * @param mixed $data Data to verify
     * @param string $hash Hash to verify against
     * @return bool Whether the hash matches
     */
    public function verifyHash($data, $hash)
    {
        return $this->hash($data) === $hash;
    }
    
    /**
     * Encrypt data with public key
     * 
     * @param mixed $data Data to encrypt
     * @param string $publicKey Public key
     * @return string Encrypted data
     */
    public function encryptWithPublicKey($data, $publicKey)
    {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        
        // Convert data to string
        $data = (string) $data;
        
        // Generate a random key for symmetric encryption
        $symmetricKey = random_bytes(32);
        
        // Encrypt the data with the symmetric key
        $encryptedData = sodium_crypto_secretbox(
            $data,
            $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES),
            $symmetricKey
        );
        
        // Encrypt the symmetric key with the public key
        $encryptedKey = sodium_crypto_box_seal($symmetricKey, $publicKey);
        
        // Combine the encrypted key, nonce, and encrypted data
        $result = base64_encode(json_encode([
            'key' => base64_encode($encryptedKey),
            'nonce' => base64_encode($nonce),
            'data' => base64_encode($encryptedData)
        ]));
        
        return $result;
    }
    
    /**
     * Decrypt data with private key
     * 
     * @param string $encryptedData Encrypted data
     * @param string $privateKey Private key
     * @param string $publicKey Public key
     * @param bool $asJson Whether to decode as JSON
     * @return mixed Decrypted data
     */
    public function decryptWithPrivateKey($encryptedData, $privateKey, $publicKey, $asJson = false)
    {
        // Decode the encrypted data
        $data = json_decode(base64_decode($encryptedData), true);
        
        // Extract the encrypted key, nonce, and encrypted data
        $encryptedKey = base64_decode($data['key']);
        $nonce = base64_decode($data['nonce']);
        $encryptedData = base64_decode($data['data']);
        
        // Decrypt the symmetric key with the private key
        $keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey($privateKey, $publicKey);
        $symmetricKey = sodium_crypto_box_seal_open($encryptedKey, $keypair);
        
        // Decrypt the data with the symmetric key
        $decrypted = sodium_crypto_secretbox_open($encryptedData, $nonce, $symmetricKey);
        
        if ($asJson) {
            return json_decode($decrypted, true);
        }
        
        return $decrypted;
    }
    
    /**
     * Generate key pair
     * 
     * @return array Key pair (public_key, private_key)
     */
    public function generateKeyPair()
    {
        $keypair = sodium_crypto_box_keypair();
        $publicKey = sodium_crypto_box_publickey($keypair);
        $privateKey = sodium_crypto_box_secretkey($keypair);
        
        return [
            'public_key' => base64_encode($publicKey),
            'private_key' => base64_encode($privateKey)
        ];
    }
}