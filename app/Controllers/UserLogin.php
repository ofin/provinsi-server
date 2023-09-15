<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LoginModel;
use CodeIgniter\API\ResponseTrait;

class UserLogin extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $model = new LoginModel();
        $data = $model->findAll();
        return $this->respond($data);
    }

    public function create()
    {
        helper(['form']);
        $rules = [
            'username' => 'required|is_unique[login.username]',
            'password' => 'required|min_length[6]',
        ];

        $data = [
            'username' => $this->request->getVar('username'),
            'password' => $this->request->getVar('password'),
            'role' => '1',

        ];

        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors());
        $model = new LoginModel();
        $model->save($data);
        $response = [
            'status' => 201,
            'error' => null,
            'message' => [
                'success' => 'Data tersimpan'
            ],
        ];
        return $this->respondCreated($response);
    }

    public function update()
    {
        helper(['form']);
        $rules = [
            'username' => 'required|is_unique[login.username]',
            'password' => 'required|min_length[6]',
        ];

        $data = [
            'username' => $this->request->getVar('username'),
            'password' => $this->request->getVar('password'),

        ];

        $model = new LoginModel();

        $find = $model->find(['username' => $data['username']]);
        if (!$find) return $this->failNotFound('data tidak ditemukan');
        $model->update($data['username'], $data);
        $respons = [
            'status' => 200,
            'error' => null,
            'message' => [
                'sukses' => 'Data diubah'
            ]
        ];
        return $this->respondCreated($respons);
    }

    public function delete($id = null)
    {
        $model = new LoginModel();
        $findbyid = $model->find(['id' => $id]);
        if (!$findbyid) return $this->failNotFound('Data Tidak ditemukan');
        $model->delete($id);
        $respons = [
            'status' => 200,
            'error' => null,
            'message' => [
                'sukses' => 'Data Dihapus'
            ]
        ];
        return $this->respondCreated($respons);
    }
}
