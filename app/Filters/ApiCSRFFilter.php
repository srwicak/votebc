<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiCSRFFilter implements FilterInterface
{
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
        // Skip for GET requests
        if ($request->getMethod() === 'get') {
            return;
        }
        
        $security = \Config\Services::security();
        
        // Check CSRF token in header
        $headerToken = $request->getHeaderLine($security->csrfHeaderName);
        
        // Check CSRF token in POST data
        $postToken = $request->getPost($security->csrfTokenName);
        
        // Check CSRF token in JSON body
        $jsonToken = null;
        $json = $request->getJSON(true);
        if (is_array($json) && isset($json[$security->csrfTokenName])) {
            $jsonToken = $json[$security->csrfTokenName];
        }
        
        // Verify token
        if (!$headerToken && !$postToken && !$jsonToken) {
            return service('response')
                ->setJSON(['error' => 'CSRF token missing'])
                ->setStatusCode(403);
        }
        
        $token = $headerToken ?: ($postToken ?: $jsonToken);
        
        if (!$security->verify($token)) {
            return service('response')
                ->setJSON(['error' => 'CSRF token invalid'])
                ->setStatusCode(403);
        }
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