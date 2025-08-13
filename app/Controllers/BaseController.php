<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['form'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;
    
    /**
     * User data from session
     * 
     * @var array|null
     */
    protected $userData = null;
    
    /**
     * Flag if user is logged in
     * 
     * @var bool
     */
    protected $isLoggedIn = false;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = \Config\Services::session();
        
        // Load user data from session if available
        if (session()->has('user_id')) {
            $userModel = new \App\Models\UserModel();
            $this->userData = $userModel->find(session()->get('user_id'));
            $this->isLoggedIn = (bool) $this->userData;
        }
    }

    protected function sendResponse($data, $code = 200)
    {
        $response = [
            'status' => 'success',
            'data' => $data
        ];
        
        return $this->response
            ->setStatusCode($code)
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->setJSON($response);
    }

    protected function sendError($message, $code = 400)
    {
        return $this->response
            ->setStatusCode($code)
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->setJSON([
                'status' => 'error',
                'error' => $message
            ]);
    }

    protected function getCurrentUser()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        if (!$authHeader || !strpos($authHeader, 'Bearer ') === 0) {
            return null;
        }

        $token = substr($authHeader, 7);
        $jwt = new \App\Libraries\JWT();
        $decoded = $jwt->decode($token);

        if (!$decoded) {
            return null;
        }

        $userModel = new \App\Models\UserModel();
        return $userModel->find($decoded['user_id']);
    }

    protected function requireAuth()
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            throw new \Exception('Unauthorized', 401);
        }
        return $user;
    }

    protected function requireRole($roles)
    {
        $user = $this->requireAuth();
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        if (!in_array($user['role'], $roles) && !$user['is_super_admin']) {
            throw new \Exception('Forbidden', 403);
        }
        
        return $user;
    }
}