<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\LoginModel;
use CodeIgniter\HTTP\RequestTrait;

class User extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    use RequestTrait;
    public function index()
    {
        $model = new LoginModel();
        $data = $model->findAll();
        return $this->respond($data);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($username = null)
    {
        $model = new LoginModel();
        $data = $model->find(['username' => $username]);
        if (!$data) return $this->failNotFound('Data Tidak ditemukan');
        return $this->respond($data[0]);
    }


    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        helper(['form']);
        $rules = [
            'username' => 'required|is_unique[login.username]',
            'nama' => 'required',
            'role' => 'required'
        ];

        $data = [
            'username' => $this->request->getVar('username'),
            'password' => $this->request->getVar('password'),
            'role' => $this->request->getVar('role'),
        ];

        // $username = $data['username'];

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

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($username = null)
    {
        $data = [
            'username' => $this->request->getVar('username'),
            'password' => $this->request->getVar('password'),
            'role' => $this->request->getVar('role'),
        ];

        // $username = $data['username'];


        $model = new LoginModel();

        $findbyid = $model->find(['username' => $data['username']]);
        if (!$findbyid) return $this->failNotFound('Data Tidak ditemukan');
        $model->update($data['username'], $data);
        $response = [
            'status' => 201,
            'error' => null,
            'message' => [
                'success' => 'Data tersimpan'
            ],
        ];
        return $this->respondCreated($response);
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
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

    public function getdatabyid($id = null)
    {
        $model = new LoginModel();
        $data = $model->find($id);
        return $this->respond($data);
    }
}
