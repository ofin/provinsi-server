<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DesaModel;
use App\Models\KabupatenModel;
use App\Models\KecamatanModel;
use App\Models\KeluargaModel;
use CodeIgniter\API\ResponseTrait;

class Kabupaten extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        // $colorArray = ["#2caffe", "#544fc5", "#00e272", "#fe6a35", "#6b8abc", "#d568fb", "#2ee0ca", "#fa4b42", "#feb56a", "#91e8e1", "#4a148c", '#f44336', '#3949ab'];

        $colorArray = [
            "#2caffe", "#544fc5", "#00e272", "#fe6a35", "#6b8abc", "#d568fb", "#2ee0ca", "#fa4b42", "#feb56a", "#91e8e1", "#4a148c", '#FF6633', '#FFB399', '#FF33FF', '#FFFF99', '#00B3E6',
            '#E6B333', '#3366E6', '#999966', '#99FF99', '#B34D4D',
            '#80B300', '#809900', '#E6B3B3', '#6680B3', '#66991A',
            '#FF99E6', '#CCFF1A', '#FF1A66', '#E6331A', '#33FFCC',
            '#66994D', '#B366CC', '#4D8000', '#B33300', '#CC80CC',
            '#66664D', '#991AFF', '#E666FF', '#4DB3FF', '#1AB399',
            '#E666B3', '#33991A', '#CC9999', '#B3B31A', '#00E680',
            '#4D8066', '#809980', '#E6FF80', '#1AFF33', '#999933',
            '#FF3380', '#CCCC00', '#66E64D', '#4D80CC', '#9900B3',
            '#E64D66', '#4DB380', '#FF4D4D', '#99E6E6', '#6666FF'
        ];
        $kabupatenModel = new KabupatenModel();
        $kecModel = new KecamatanModel();
        $desaModel = new DesaModel();
        $keluargaModel = new KeluargaModel();
        $kabupatens = $kabupatenModel->findAll();
        $i = 0;
        $targetz = 0;
        foreach ($kabupatens as $kab) {
            $rand = array_rand($colorArray);

            $dpt = $desaModel->where(['id_kab' => $kab['id_kab']])->selectSum('jml_dpt')->find();
            $kabupatens[$i]['jml_dpt'] = (int) $dpt[0]['jml_dpt'];
            $dptTerdata = $keluargaModel->where(['id_kab' => $kab['id_kab']])->selectSum('jml_anggota')->find();
            $kabupatens[$i]['jml_dpt_terdata'] = (int) $dptTerdata[0]['jml_anggota'];
            $target = $desaModel->where(['id_kab' => $kab['id_kab']])->selectSum('target')->find();
            $kabupatens[$i]['target'] = (int) $target[0]['target'];
            $kkTerdata = $keluargaModel->where(['id_kab' => $kab['id_kab']])->selectCount('no_kk')->find();
            $kabupatens[$i]['kk_terdata'] = (int) $kkTerdata[0]['no_kk'];
            // $kecamatans[$i]['persen_kk'] = $target[0]['target'] == 0 ? 0 : $kkTerdata[0]['no_kk'] / $target[0]['target'];
            // $kecamatans[$i]['persen_dpt'] = $dpt[0]['jml_dpt'] == 0 ? 0 : $dptTerdata[0]['jml_anggota'] / $dpt[0]['jml_dpt'];
            $kabupatens[$i]['color'] = $colorArray[$rand];
            $targetz += $target[0]['target'];
            $jmlKec = $kecModel->where(['id_kab' => $kab['id_kab']])->selectCount('id_kab')->find();
            $kabupatens[$i]['jml_kec'] = (int) $jmlKec[0]['id_kab'];

            unset($colorArray[$rand]);
            $i++;
        }
        // $kecamatans['target'] = $targetz;
        // return $this->respond(['kec' => $kecamatans, 'target' => $targetz]);
        return $this->respond($kabupatens);
        //
    }

    public function delete()
    {
        $id = $this->request->getPost('id_kab');
        $kabModel = new KabupatenModel();

        $data = $kabModel->delete($id);
        $affectedRows = $kabModel->affectedRows();
        if (!$affectedRows) {
            return $this->fail('Data login tidak terhapus');
        }

        return $this->respond($affectedRows . ' Data Terhapus', 200);
    }

    public function update()
    {
        $post = $this->request->getPost();
        $kabModel = new KabupatenModel();
        $kabModel->update($post['id_kab'], $post);
        $affectedRows = $kabModel->affectedRows();
        if (!$affectedRows) {
            return $this->fail([
                'msg'   => 'Data tidak tersimpan'
            ]);
        }
        $info = 'Data Diubah';

        return $this->respond($info, 200);
    }
}
