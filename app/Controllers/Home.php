<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class Home extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        echo 'zz';
    }

    public function encrypt()
    {
        $config  = new \Config\Encryption();
        $encrypter = \Config\Services::encrypter($config);
        $token = base64_encode($encrypter->encrypt('Rahasia'));

        return $this->respond($token, 200);
    }

    public function decrypt()
    {
        $config  = new \Config\Encryption();
        $encrypter = \Config\Services::encrypter($config);
        $token = $encrypter->decrypt(base64_decode('o5UL1ZwJfQwq7BEgAc2fH381vMl5qqZAzC76TpQhOqpFIMkZ61kxAI4smEX0PqQ1Juby3wdxeFN2QhJDafFFHAtYTvAI6fDEFOWYkSLNFGSni9AqFCCW2S2/'));

        return $this->respond($token, 200);
    }

    public function dokumentasi()
    {
        helper('filesystem');

        $no_kk = $this->request->getPost('no_kk');

        $data = directory_map(FCPATH . 'uploads/' . $no_kk);

        return $this->respond($data, 200);
    }
}
