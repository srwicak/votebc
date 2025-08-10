<?php

namespace App\Libraries;

class RateLimiter
{
    protected $cache;
    protected $ipAddress;
    
    public function __construct()
    {
        $this->cache = \Config\Services::cache();
        $this->ipAddress = service('request')->getIPAddress();
    }
    
    /**
     * Check if the current IP address has exceeded the rate limit
     *
     * @param string $key Unique identifier for the rate limit
     * @param int $maxAttempts Maximum number of attempts allowed
     * @param int $decayMinutes Number of minutes until the rate limit resets
     * @return bool True if rate limit exceeded, false otherwise
     */
    public function tooManyAttempts($key, $maxAttempts, $decayMinutes)
    {
        $key = $this->getKey($key);
        
        if ($this->cache->get($key) === null) {
            $this->cache->save($key, 0, $decayMinutes * 60);
        }
        
        $attempts = (int) $this->cache->get($key);
        
        if ($attempts >= $maxAttempts) {
            return true;
        }
        
        $this->cache->save($key, $attempts + 1, $decayMinutes * 60);
        
        return false;
    }
    
    /**
     * Get the number of attempts left
     *
     * @param string $key Unique identifier for the rate limit
     * @param int $maxAttempts Maximum number of attempts allowed
     * @return int Number of attempts left
     */
    public function retriesLeft($key, $maxAttempts)
    {
        $key = $this->getKey($key);
        $attempts = (int) $this->cache->get($key);
        
        return $maxAttempts - $attempts;
    }
    
    /**
     * Reset the rate limit for the given key
     *
     * @param string $key Unique identifier for the rate limit
     * @return void
     */
    public function resetAttempts($key)
    {
        $key = $this->getKey($key);
        $this->cache->delete($key);
    }
    
    /**
     * Get the full key including IP address
     *
     * @param string $key Unique identifier for the rate limit
     * @return string Full key
     */
    protected function getKey($key)
    {
        return 'rate_limit:' . $key . ':' . $this->ipAddress;
    }
}