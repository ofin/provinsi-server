<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\AnggotaKeluargaModel;
use App\Models\DesaModel;
use App\Models\KecamatanModel;
use App\Models\KeluargaModel;
use App\Models\LoginModel;
use App\Models\TimModel;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;

class Auth extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        return $this->respond('Tes');
    }

    public function doLogin()
    {
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $loginModel = new LoginModel();
        $loginData = $loginModel->find($username);
        if (is_null($loginData)) {
            return $this->respond('Username Salah', 400);
        }

        $config  = new \Config\Encryption();
        $encrypter = \Config\Services::encrypter($config);
        $decryptedPassword = $encrypter->decrypt(base64_decode($loginData['password']));

        if ($password != $decryptedPassword) {
            return $this->respond('Password Salah', 400);
        }

        $role = $loginData['role'];
        $userData = '';
        $payload = [];

        $iat = time();
        $exp = $iat + (3600 * 12);
        // $exp = $iat + (60);

        if ($role == '1') { // TIM
            $timModel = new TimModel();
            $userData = $timModel->where(['username' => $username])->first();

            if ($userData == null) {
                return $this->respond('User tidak ditemukan', 400);
            }

            $payload = array(
                "iat"       => $iat, //Time the JWT issued at
                "exp"       => $exp, // Expiration time of token
                "username"  => $userData['username'],
                'nama'      => $userData['nama'],
                'id_kab'    => $userData['id_kab'],
                'nama_kab'  => $userData['nama_kab'],
                'id_kec'    => $userData['id_kec'],
                'nama_kec'  => $userData['nama_kec'],
                'id_desa'   => $userData['id_desa'],
                'nama_desa' => $userData['nama_desa'],
                'role'      => $role,
            );
        } else if ($role == '2') { // ADMIN
            $adminModel = new AdminModel();
            $userData = $adminModel->where(['username' => $username])->first();

            if ($userData == null) {
                return $this->respond('User tidak ditemukan', 400);
            }

            $payload = array(
                "iat"       => $iat, //Time the JWT issued at
                "exp"       => $exp, // Expiration time of token
                "username"  => $userData['username'],
                'nama'      => $userData['nama'],
                'role'      => $role,
            );
        } else {
            return $this->respond('Role tidak Ditemukan', 400);
        }

        $key = getenv('TOKEN_SECRET');
        $tokenz = JWT::encode($payload, $key, 'HS256');

        $config  = new \Config\Encryption();
        $encrypter = \Config\Services::encrypter($config);
        $token = base64_encode($encrypter->encrypt($tokenz));

        if ($role == '1') {
            $response = [
                'nama'      => $userData['nama'],
                "username"  => $userData['username'],
                'role'      => $role,
                'id_kab'    => $userData['id_kab'],
                'nama_kab'  => $userData['nama_kab'],
                'id_kec'    => $userData['id_kec'],
                'nama_kec'  => $userData['nama_kec'],
                'id_desa'   => $userData['id_desa'],
                'nama_desa' => $userData['nama_desa'],
                'token'     => $token
            ];
        }
        else if ($role == '2') {
            $response = [
                'nama'      => $userData['nama'],
                "username"  => $userData['username'],
                'role'      => $role,
                'token'     => $token
            ];
        }


        return $this->respond($response, 200);
    }

    // public function updateNik()
    // {
    //     $anggotaModel = new AnggotaKeluargaModel();

    //     $anggotas = $anggotaModel->findAll();

    //     $i = 1;
    //     foreach ($anggotas as $anggota) {
    //         $anggotaModel->update($anggota['nik'], ['nik' => $i]);
    //         $i++;
    //     }
    // }
    
    // public function updateNikKK()
    // {
    //     $keluargaModel = new KeluargaModel();
    //     $keluargas = $keluargaModel->findAll();
    //     foreach ($keluargas as $keluarga) {
    //         $kk = [];
    //         $anggotaModel = new AnggotaKeluargaModel();
    //         $kk = $anggotaModel->where(['no_kk' => $keluarga['no_kk'], 'status' => 'Kepala Keluarga'])->find();
    //         $kkz = $kk[0];
    //         $keluargaModel->update($keluarga['no_kk'], ['nik_kepala' => $kkz['nik']]);
    //     }
    // }

    // public function updateKab()
    // {
    //     $kecModel = new KecamatanModel();
    //     $desaModel = new DesaModel();

    //     $kecs = $kecModel->findAll();
    //     foreach ($kecs as $kec) {
    //         $data = [
    //             'id_kab' => $kec['id_kab']
    //         ];

    //         $desaModel->where(['id_kec' => $kec['id_kec']])->set($data)->update();
    //     }
    // }
}
