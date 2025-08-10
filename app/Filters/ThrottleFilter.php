<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\RateLimiter;

class ThrottleFilter implements FilterInterface
{
    /**
     * Rate limit configuration
     * 
     * @var array
     */
    protected $config = [
        'login' => [
            'maxAttempts' => 5,
            'decayMinutes' => 10
        ],
        'register' => [
            'maxAttempts' => 3,
            'decayMinutes' => 60
        ],
        'vote' => [
            'maxAttempts' => 10,
            'decayMinutes' => 60
        ],
        'api' => [
            'maxAttempts' => 60,
            'decayMinutes' => 1
        ]
    ];
    
    /**
     * Do whatever processing this filter needs to do.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (empty($arguments)) {
            return;
        }
        
        $type = $arguments[0];
        
        if (!isset($this->config[$type])) {
            return;
        }
        
        $config = $this->config[$type];
        $rateLimiter = new RateLimiter();
        
        if ($rateLimiter->tooManyAttempts($type, $config['maxAttempts'], $config['decayMinutes'])) {
            $response = service('response');
            
            $retryAfter = $config['decayMinutes'] * 60;
            $retriesLeft = 0;
            
            $response->setHeader('Retry-After', $retryAfter);
            $response->setHeader('X-RateLimit-Limit', $config['maxAttempts']);
            $response->setHeader('X-RateLimit-Remaining', $retriesLeft);
            
            return $response->setJSON([
                'error' => 'Too many attempts. Please try again later.',
                'retryAfter' => $retryAfter
            ])->setStatusCode(429);
        }
        
        return;
    }
    
    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}