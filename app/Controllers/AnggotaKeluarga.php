<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AnggotaKeluargaModel;
use CodeIgniter\API\ResponseTrait;

class AnggotaKeluarga extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $noKK = $this->request->getPost('no_kk');

        $anggotaModel = new AnggotaKeluargaModel();
        $data = $anggotaModel->where(['no_kk' => $noKK])->orderBy('no_urut', 'ASC')->findAll();
        return $this->respond($data, 200);
    }
}
