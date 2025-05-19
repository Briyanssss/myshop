<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ProfileController extends BaseController
{
    public function index()
    {
        $session = session();
        $data = [
            'username'    => $session->get('username'),
            'role'        => $session->get('role'),
            'email'       => $session->get('email'),
            'login_time'  => $session->get('login_time'),
            'status'      => 'Login Berhasil'
        ];
    
        return view('v_profile', $data);
    }
    
}
