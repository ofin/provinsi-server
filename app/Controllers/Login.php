<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\LoginModel;
use Firebase\JWT\JWT;

class Login extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        $rol = $this->request->getVar('role');
        $where = ['username' => $username, 'password' => $password, 'role' => $rol];

        $model = new LoginModel();

        $user = $model->where($where)->first();
        if (!$user) return $this->failNotFound('Username Tidak ditemukan');
        elseif ($password != $user['password']) return $this->fail('Password Salah');
        elseif ($rol != $user['role']) return $this->fail('Role Salah');
        else

            $key = getenv('TOKEN_SECRET');
        $payload = [
            'iat' => 1356999524,
            'nbf' => 1357000000,
            'uid' => $user['username'],
        ];

        $token = JWT::encode($payload, $key, 'HS256');
        return $this->respond($token);
    }
}
