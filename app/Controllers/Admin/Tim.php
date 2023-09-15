<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TimModel;
use App\Models\LoginModel;
use App\Models\KeluargaModel;
use CodeIgniter\API\ResponseTrait;

class Tim extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $model = new TimModel();

        $post = $this->request->getPost();

        $where = [];
        $tim = [];
        if ($post['semua'] == 'false') {
            if ($post['id_kab'] != '') {
                $where['id_kab'] = $post['id_kab'];
            }
            if ($post['id_kec'] != '') {
                $where['id_kec'] = $post['id_kec'];
            }
            if ($post['id_desa'] != '') {
                $where['id_desa'] = $post['id_desa'];
            }
            $tim = $model->where($where)->findAll();
        } else {
            $tim = $model->findAll();
        }
        $loginModel = new LoginModel();
        $config  = new \Config\Encryption();
        $encrypter = \Config\Services::encrypter($config);

        $i = 0;
        foreach ($tim as $timz) {
            $loginData = $loginModel->find($timz['username']);
            $tim[$i]['password'] = $encrypter->decrypt(base64_decode($loginData['password']));
            $i += 1;
        }
        return $this->respond($tim);
    }

    public function insert()
    {
        $config  = new \Config\Encryption();
        $encrypter = \Config\Services::encrypter($config);

        $post = $this->request->getPost();
        $tim = [
            'username' => $post['username'],
            'nama' => $post['nama'],
            'id_kab' => $post['id_kab'],
            'nama_kab' => $post['nama_kab'],
            'id_kec' => $post['id_kec'],
            'nama_kec' => $post['nama_kec'],
            'id_desa' => $post['id_desa'],
            'nama_desa' => $post['nama_desa'],
        ];
        $encryptpassword = base64_encode($encrypter->encrypt($post['password']));

        $timModel = new TimModel();
        $timModel->insert($tim);
        $affectedRows = $timModel->affectedRows();
        if (!$affectedRows) {
            return $this->fail([
                'msg'   => 'Data tidak tersimpan'
            ]);
        }

        $login = [
            'username' => $post['username'],
            'password' => $encryptpassword,
            'role' => '1',
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

    // public function show($id = null)
    // {
    //     $model = new TimModel();
    //     $data = $model->find(['username' => $id]);
    //     if (!$data) return $this->failNotFound('Data Tidak ditemukan');
    //     return $this->respond($data[0]);
    // }

    // public function showkab()
    // {
    //     $model = new KabupatenModel();
    //     $data = $model->findAll();
    //     return $this->respond($data);
    // }

    // public function showkec($id = null)
    // {
    //     $model = new KecamatanModel();
    //     $where['id_kab'] = $id;
    //     $data = $model->where($where);
    //     return $this->respond($data);
    // }

    // public function create()
    // {
    //     helper(['form']);
    //     $rules = [
    //         'username' => 'required|is_unique[tim.username]',
    //         'nama' => 'required',
    //         'password' => 'required',
    //         'id_kab' => 'required'
    //     ];

    //     $data = [
    //         'username' => $this->request->getVar('username'),
    //         'nama' => $this->request->getVar('nama'),
    //         'id_kab' => $this->request->getVar('id_kab'),
    //         'nama_kab' => $this->request->getVar('nama_kab'),
    //         'id_kec' => $this->request->getVar('id_kec'),
    //         'nama_kec' => $this->request->getVar('nama_kec'),
    //         'id_desa' => $this->request->getVar('id_desa'),
    //         'nama_desa' => $this->request->getVar('nama_desa')
    //     ];

    //     $datauser = [
    //         'username' => $this->request->getVar('username'),
    //         'password' => $this->request->getVar('password'),
    //         'role' => '2'
    //     ];

    //     if (!$this->validate($rules)) return $this->fail($this->validator->getErrors());
    //     $model = new TimModel();
    //     $usermodel = new LoginModel();
    //     $model->save($data);
    //     $usermodel->save($datauser);
    //     $response = [
    //         'status' => 201,
    //         'error' => null,
    //         'message' => [
    //             'success' => 'Data tersimpan'
    //         ],
    //     ];
    //     return $this->respondCreated($response);
    // }

    // public function update($username = null)
    // {
    //     $data = [
    //         'username' => $this->request->getVar('username'),
    //         'nama' => $this->request->getVar('nama'),
    //         'id_kab' => $this->request->getVar('id_kab'),
    //         'nama_kab' => $this->request->getVar('nama_kab'),
    //         'id_kec' => $this->request->getVar('id_kec'),
    //         'nama_kec' => $this->request->getVar('nama_kec'),
    //         'id_desa' => $this->request->getVar('id_desa'),
    //         'nama_desa' => $this->request->getVar('nama_desa')
    //     ];

    //     $model = new TimModel();
    //     $findbyid = $model->find(['username' => $data['username']]);
    //     if (!$findbyid) return $this->failNotFound('Data Tidak ditemukan');
    //     $model->update($data['username'], $data);
    //     $respons = [
    //         'status' => 200,
    //         'error' => null,
    //         'message' => [
    //             'sukses' => 'Data diubah'
    //         ]
    //     ];
    //     return $this->respondCreated($respons);
    // }

    public function delete()
    {
        $id = $this->request->getPost('username');

        $loginModel = new LoginModel();

        // return $this->respond($id, 200);

        $data = $loginModel->delete($id);
        $affectedRows = $loginModel->affectedRows();
        if (!$affectedRows) {
            return $this->fail('Data login tidak terhapus');
        }

        $timModel = new TimModel();
        $data2 = $timModel->delete($id);
        $affectedRows = $timModel->affectedRows();
        if (!$affectedRows) {
            return $this->fail('Data tim tidak terhapus');
        }
        // return $this->respond($loginModel->errors(), 200);
        return $this->respond($affectedRows . ' Data Terhapus', 200);
    }

    public function Detail()
    {
        $post = $this->request->getPost();
        $keluargaModel = new KeluargaModel();
        $data = $keluargaModel->where('nama_petugas', $post['nama'])->findAll();

        // if (count($data) < 1) {
        //     return $this->fail('Data Tidak Ada');
        // }

        return $this->respond($data, 200);
    }

    public function ubah()
    {
        $config  = new \Config\Encryption();
        $encrypter = \Config\Services::encrypter($config);

        $post = $this->request->getPost();
        $tim = [
            'username' => $post['username'],
            'nama' => $post['nama'],
            'id_kab' => $post['id_kab'],
            'nama_kab' => $post['nama_kab'],
            'id_kec' => $post['id_kec'],
            'nama_kec' => $post['nama_kec'],
            'id_desa' => $post['id_desa'],
            'nama_desa' => $post['nama_desa'],
        ];
        $encryptpassword = base64_encode($encrypter->encrypt($post['password']));

        $adminModel = new TimModel();
        $adminModel->update($tim['username'], $tim);
        // $affectedRows = $adminModel->affectedRows();
        // if (!$affectedRows) {
        //     return $this->fail([
        //         'msg'   => 'Data Admin tidak tersimpan'
        //     ]);
        // }

        $login = [
            'username' => $post['username'],
            'password' => $encryptpassword,
            'role' => '1',
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

    public function getdatabyid($id = null)
    {
        $model = new TimModel();
        $data = $model->find($id);
        return $this->respond($data);
    }
}
