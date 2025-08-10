<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (!$authHeader || !strpos($authHeader, 'Bearer ') === 0) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'Unauthorized']);
        }

        $token = substr($authHeader, 7);
        $jwt = new \App\Libraries\JWT();
        $decoded = $jwt->decode($token);

        if (!$decoded) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'Invalid token']);
        }

        // Token valid, lanjutkan request
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}