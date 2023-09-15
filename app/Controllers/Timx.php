<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TimModel;
use CodeIgniter\API\ResponseTrait;

class Timx extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $model = new TimModel();

        $data = [
            'tims' => $model->paginate(3),
            'pager' => $model->pager
        ];

        return $this->respond($data);
    }
}
