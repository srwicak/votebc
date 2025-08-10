<?php

namespace App\Controllers;

class Session extends BaseController
{
    public function set()
    {
        // Log request
        log_message('debug', 'Session set request received');
        
        // Accept both AJAX and regular requests
        $input = $this->request->getJSON();
        
        if (!$input) {
            $input = (object) $this->request->getPost();
        }
        
        log_message('debug', 'Session data: ' . json_encode($input));
        
        if (isset($input->token)) {
            session()->set('auth_token', $input->token);
            log_message('debug', 'Token stored in session');
            return $this->response->setJSON(['status' => 'success']);
        } else {
            log_message('error', 'Token not found in request');
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Token not found']);
        }
    }
    
    public function logout()
    {
        log_message('debug', 'Logout request received');
        session()->remove('auth_token');
        return redirect()->to('/login')->with('message', 'Logout berhasil');
    }
}