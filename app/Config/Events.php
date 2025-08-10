<?php

namespace Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\FrameworkException;

Events::on('pre_system', function () {
    if (ENVIRONMENT !== 'testing') {
        if (ini_get('zlib.output_compression')) {
            throw FrameworkException::forEnabledZlibOutputCompression();
        }

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        ob_start(function ($buffer) {
            return $buffer;
        });
    }

    // Add CORS headers
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        
        exit(0);
    }
});