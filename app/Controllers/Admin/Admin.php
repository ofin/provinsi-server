<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LoginModel;
use App\Models\AdminModel;

use CodeIgniter\API\ResponseTrait;


class Admin extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $model = new AdminModel();
        $admin = $model->findAll();

        $loginModel = new LoginModel();
        $config  = new \Config\Encryption();
        $encrypter = \Config\Services::encrypter($config);

        $i = 0;
        foreach ($admin as $key => $admins) {
            $loginData = $loginModel->find($admins['username']);
            $admin[$i]['password'] = $encrypter->decrypt(base64_decode($loginData['password']));
            $i += 1;
        }

        return $this->respond($admin, 200);
    }

    public function Tambah()
    {
        $config  = new \Config\Encryption();
        $encrypter = \Config\Services::encrypter($config);

        $post = $this->request->getPost();
        $admin = [
            'username' => $post['username'],
            'nama' => $post['nama'],
        ];
        $encryptpassword = base64_encode($encrypter->encrypt($post['password']));

        $adminModel = new AdminModel();
        $adminModel->insert($admin);
        $affectedRows = $adminModel->affectedRows();
        if (!$affectedRows) {
            return $this->fail([
                'msg'   => 'Data tidak tersimpan'
            ]);
        }

        $login = [
            'username' => $post['username'],
            'password' => $encryptpassword,
            'role' => '2',
        ];
        $loginModel = new LoginModel();

        $loginModel->insert($login);
        $affectedRows = $loginModel->affectedRows();
        if (!$affectedRows) {
            return $this->fail([
                'msg'   => 'Data tidak tersimpan'
            ]);
        }

        $info = 'Data Tersimpan';
        return $this->respond([
            'msg'   => $info
        ], 200);
    }

    public function ubah()
    {
        $config  = new \Config\Encryption();
        $encrypter = \Config\Services::encrypter($config);

        $post = $this->request->getPost();
        $admin = [
            'username' => $post['username'],
            'nama' => $post['nama'],
        ];
        $encryptpassword = base64_encode($encrypter->encrypt($post['password']));

        $adminModel = new AdminModel();
        $adminModel->update($admin['username'], $admin);
        // $affectedRows = $adminModel->affectedRows();
        // if (!$affectedRows) {
        //     return $this->fail([
        //         'msg'   => 'Data Admin tidak tersimpan'
        //     ]);
        // }

        $login = [
            'username' => $post['username'],
            'password' => $encryptpassword,
            'role' => '2',
        ];
        $loginModel = new LoginModel();

        $loginModel->update($login['username'], $login);
        // $affectedRows = $loginModel->affectedRows();
        // if (!$affectedRows) {
        //     return $this->fail([
        //         'msg'   => 'Data Login tidak tersimpan'
        //     ]);
        // }

        $info = 'Data Tersimpan';
        return $this->respond([
            'msg'   => $info
        ], 200);
    }

    public function delete()
    {
        $id = $this->request->getPost('username');

        $loginModel = new LoginModel();

        // return $this->respond($id, 200);

        $loginModel->delete(['username' => $id]);
        $affectedRows = $loginModel->affectedRows();
        if (!$affectedRows) {
            return $this->fail('Data login tidak terhapus');
        }

        $adminModel = new AdminModel();
        $adminModel->delete(['username' => $id]);
        $affectedRows = $adminModel->affectedRows();
        if (!$affectedRows) {
            return $this->fail('Data tim tidak terhapus');
        }
        // return $this->respond($loginModel->errors(), 200);
        return $this->respond($affectedRows . ' Data Terhapus', 200);
    }
}
