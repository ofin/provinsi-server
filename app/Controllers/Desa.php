<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\DesaModel;
use App\Models\KeluargaModel;
use CodeIgniter\HTTP\RequestTrait;

class Desa extends BaseController
{
    use ResponseTrait;
    public function index($idKec = null)
    {
        $desaModel = new DesaModel();
        $keluargaModel = new KeluargaModel();
        $desas = $desaModel->where('id_kec', $idKec)->findAll();
        $i = 0;

        foreach ($desas as $desa) {
            // $dpt = $desaModel->where(['id_desa' => $desa['id_desa']])->selectSum('jml_dpt')->find();
            // $desas[$i][$i]['jml_dpt'] = $dpt[0]['jml_dpt'];
            $dptTerdata = $keluargaModel->where(['id_desa' => $desa['id_desa']])->selectSum('jml_anggota')->find();
            $desas[$i]['jml_dpt_terdata'] = $dptTerdata[0]['jml_anggota'] == null ? '0' :  $dptTerdata[0]['jml_anggota'];
            $kkTerdata = $keluargaModel->where(['id_desa' => $desa['id_desa']])->selectCount('no_kk')->find();
            $desas[$i]['kk_terdata'] = $kkTerdata[0]['no_kk'];
            $i++;
        }

        return $this->respond($desas);
    }

    public function tambah()
    {
        $post = $this->request->getPost();
        $desaModel = new DesaModel();
        $desaModel->insert($post);
        $affectedRows = $desaModel->affectedRows();
        if (!$affectedRows) {
            return $this->fail([
                'msg'   => 'Data tidak tersimpan'
            ]);
        }

        return $this->respond([
            'msg'   => 'Data Tersimpan'
        ], 200);
    }

    public function getDesaById($id)
    {
        $desaModel = new DesaModel();
        $data = $desaModel->where('id_kec', $id)->findAll();
        return $this->respond($data);
    }

    public function update()
    {
        $desaModel = new DesaModel();
        $post = $this->request->getPost();

        $desaModel->update($post['id_desa'], $post);
        $affectedRows = $desaModel->affectedRows();
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

    public function delete()
    {
        $id = $this->request->getPost('id_desa');

        $desaModel = new DesaModel();

        // return $this->respond($id, 200);

        $desaModel->delete(['id_desa' => $id]);
        $affectedRows = $desaModel->affectedRows();
        if (!$affectedRows) {
            return $this->fail('Data login tidak terhapus');
        }


        return $this->respond($affectedRows . ' Data Terhapus', 200);
    }
}
