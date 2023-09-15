<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnggotaKeluargaModel;
use App\Models\DesaModel;
use App\Models\KabupatenModel;
use App\Models\KecamatanModel;
use App\Models\KeluargaModel;
use CodeIgniter\API\ResponseTrait;

class Pendataan extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $semua = $this->request->getPost('semua');
        $tanggal = $this->request->getPost('tgl');
        $kab = $this->request->getPost('kab');
        $kec = $this->request->getPost('kec');
        $desa = $this->request->getPost('desa');

        $keluargaModel = new KeluargaModel();

        $where = [];
        if ($semua == 'false') {
            $where['date(created_at)'] = $tanggal;
        }
        if ($kab == "") {
            $data = $keluargaModel->findAll();
        } else {
            $kab != "" && $where['id_kab'] = $kab;
            $kec != "" && $where['id_kec'] = $kec;
            $desa != "" && $where['id_desa'] = $desa;

            $data = $keluargaModel->where($where)->findAll();
        }
        return $this->respond($data, 200);
    }

    public function delete()
    {
        $id = $this->request->getPost('no_kk');

        $keluargaModel = new KeluargaModel();

        $keluargaModel->delete($id);
        $affectedRows = $keluargaModel->affectedRows();
        if (!$affectedRows) {
            return $this->fail('Data Keluarga tidak terhapus');
        }
        $anggotaModel = new AnggotaKeluargaModel();
        $anggotaModel->where('no_kk', $id)->delete();
        $affectedRowsz = $anggotaModel->affectedRows();
        if (!$affectedRowsz) {
            return $this->fail('Data Anggota Keluarga tidak terhapus');
        }
        return $this->respond($affectedRows . ' Data Terhapus', 200);

        // return $this->respond($data, 200);
    }

    public function pieChart()
    {
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
        $post = $this->request->getPost();
        $where = [];

        $kelas = $post['categories'];
        $field = $post['field'];
        $names = $post['names'];

        if ($post['semua'] == 'false') {
            $where['date(created_at)'] = $post['tgl'];
        }

        if ($post['id_kab'] != '') {
            $where['id_kab'] = $post['id_kab'];
        }
        if ($post['id_kec'] != '') {
            $where['id_kec'] = $post['id_kec'];
        }
        if ($post['id_desa'] != '') {
            $where['id_desa'] = $post['id_desa'];
        }

        $keluargaModel = new KeluargaModel();
        $all = $keluargaModel->where($where)->selectCount('no_kk')->find();
        $total = (int) $all[0]['no_kk'];

        $data = [];
        $i = 0;
        foreach ($kelas as $kls) {
            $rand = array_rand($colorArray);
            $where[$field] = $kls;
            $dt = $keluargaModel->where($where)->selectCount('no_kk')->find();
            $data[] = [
                'name'  => $names[$i],
                'persen' => $dt[0]['no_kk'] === 0 || $total === 0 ? 0 : ((int) $dt[0]['no_kk'] / $total) * 100,
                'y'     => (int) $dt[0]['no_kk'],
                'color' => $colorArray[$rand]
            ];
            $i++;
            unset($colorArray[$rand]);
        }

        return $this->respond($data, 200);
    }

    public function stackedColumnsChart()
    {
        $post = $this->request->getPost();
        $where = [];

        $kelas = $post['categories'];
        $field = $post['field'];
        $names = $post['names'];
        $model = null;
        $categories = [];
        $searchField = '';
        $cat = '';

        if ($post['semua'] == 'false') {
            $where['date(created_at)'] = $post['tgl'];
        }
        if ($post['id_kab'] == '') {
            $model = new KabupatenModel();
            $categories = $model->findAll();
            $searchField = 'id_kab';
            $cat = 'nama_kab';
        }
        if ($post['id_kab'] != '') {
            // $where['id_kab'] = $post['id_kab'];
            $model = new KecamatanModel();
            $categories = $model->where(['id_kab' => $post['id_kab']])->findAll();
            $searchField = 'id_kec';
            $cat = 'nama_kec';
        }
        if ($post['id_kec'] != '') {
            // $where['id_kec'] = $post['id_kec'];
            $model = new DesaModel();
            $categories = $model->where(['id_kec' => $post['id_kec']])->findAll();
            $searchField = 'id_desa';
            $cat = 'nama_desa';
        }
        if ($post['id_desa'] != '') {
            $where['id_desa'] = $post['id_desa'];
        }

        $keluargaModel = new KeluargaModel();
        $series = [];
        $i = 0;
        foreach ($kelas as $kls) {
            $where[$field] = $kls;
            $catData = [];
            $catz = [];
            foreach ($categories as $category) {
                $where[$searchField] = $category[$searchField];
                $dt = $keluargaModel->where($where)->selectCount('no_kk')->find();
                $catData[] = (int) $dt[0]['no_kk'];
                $catz[] = $category[$cat];
            }
            $series[] = [
                'name'  => $names[$i],
                'data' => $catData
            ];
            $i++;
        }
        $data = [
            'series' => $series,
            'categories' => $catz
        ];

        return $this->respond($data, 200);
    }

    public function pieChartBantuan()
    {
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
        $post = $this->request->getPost();
        // $like = [];

        $penerima = [
            '-',
            'Subsidi Pemerintah',
            'Bantuan Nelayan',
            'Bantuan UMKM',
            'Beasiswa',
            'Bantuan Sosial'
        ];

        $where = [];

        if ($post['semua'] == 'false') {
            $where['date(created_at)'] = $post['tgl'];
        }

        if ($post['id_kab'] != '') {
            $where['id_kab'] = $post['id_kab'];
        }
        if ($post['id_kec'] != '') {
            $where['id_kec'] = $post['id_kec'];
        }
        if ($post['id_desa'] != '') {
            $where['id_desa'] = $post['id_desa'];
        }

        $keluargaModel = new KeluargaModel();
        $all = $keluargaModel->where($where)->selectCount('no_kk')->find();
        $total = (int) $all[0]['no_kk'];
        $data = [];
        foreach ($penerima as $kls) {
            $rand = array_rand($colorArray);
            $like = [
                'bantuan' => $kls
            ];
            $dt = $keluargaModel->where($where)->like($like)->selectCount('no_kk')->find();
            $data[] = [
                'name'  => $kls == '-' ? 'Tidak Menerima' : 'Penerima Bantuan ' . $kls,
                'persen' => $dt[0]['no_kk'] === 0 || $total === 0 ? 0 : ((int) $dt[0]['no_kk'] / $total) * 100,
                'y'     => (int) $dt[0]['no_kk'],
                'color' => $colorArray[$rand]
            ];
            unset($colorArray[$rand]);
        }

        return $this->respond($data, 200);
    }

    public function stackedColumnsChartBantuan()
    {
        $post = $this->request->getPost();
        $where = [];

        $kelas = [
            '-',
            'Subsidi Pemerintah',
            'Bantuan Nelayan',
            'Bantuan UMKM',
            'Beasiswa',
            'Bantuan Sosial'
        ];
        // $field = $post['field'];
        // $names = $post['names'];
        $model = null;
        $categories = [];
        $searchField = '';
        $cat = '';

        if ($post['semua'] == 'false') {
            $where['date(created_at)'] = $post['tgl'];
        }
        if ($post['id_kab'] == '') {
            $model = new KabupatenModel();
            $categories = $model->findAll();
            $searchField = 'id_kab';
            $cat = 'nama_kab';
        }
        if ($post['id_kab'] != '') {
            // $where['id_kab'] = $post['id_kab'];
            $model = new KecamatanModel();
            $categories = $model->where(['id_kab' => $post['id_kab']])->findAll();
            $searchField = 'id_kec';
            $cat = 'nama_kec';
        }
        if ($post['id_kec'] != '') {
            // $where['id_kec'] = $post['id_kec'];
            $model = new DesaModel();
            $categories = $model->where(['id_kec' => $post['id_kec']])->findAll();
            $searchField = 'id_desa';
            $cat = 'nama_desa';
        }
        if ($post['id_desa'] != '') {
            $where['id_desa'] = $post['id_desa'];
        }

        $keluargaModel = new KeluargaModel();
        $series = [];
        $i = 0;
        foreach ($kelas as $kls) {
            $like = [
                'bantuan' => $kls
            ];
            $catData = [];
            $catz = [];
            foreach ($categories as $category) {
                $where[$searchField] = $category[$searchField];
                $dt = $keluargaModel->where($where)->like($like)->selectCount('no_kk')->find();
                $catData[] = (int) $dt[0]['no_kk'];
                $catz[] = $category[$cat];
            }
            $series[] = [
                'name'  => $kls == '-' ? 'Tidak Menerima' : 'Penerima Bantuan ' . $kls,
                'data' => $catData
            ];
            $i++;
        }
        $data = [
            'series' => $series,
            'categories' => $catz
        ];

        return $this->respond($data, 200);
    }

    public function pieChartProgres()
    {
        $post = $this->request->getPost();
        $where = [];
        if ($post['id_kab'] == '') {
            $kabModel = new KabupatenModel();
            $kabs = $kabModel->findAll();
            foreach ($kabs as $kab) {
                $keluargaModel = new KeluargaModel();
                $tercapai = $keluargaModel->where(['id_kab' => $kab['id_kab']])->selectCount('no_kk')->find();
                $data = [
                    'tercapai' => $tercapai
                ];
                $desaModel = new DesaModel();
                $target = $desaModel->where(['id_kab' => $kab['id_kab']])->selectSum('target')->find();
                $data = [
                    'target' => $target
                ];
            }
        } else {
            if ($post['id_kab'] != '') {
                $where['id_kab'] = $post['id_kab'];
            }
            if ($post['id_kec'] != '') {
                $where['id_kec'] = $post['id_kec'];
            }
            if ($post['id_desa'] != '') {
                $where['id_desa'] = $post['id_desa'];
            }
            $data = [];

            $keluargaModel = new KeluargaModel();
            $tercapai = $keluargaModel->where($where)->selectCount('no_kk')->find();
            $data = [
                'tercapai' => $tercapai[0]['no_kk']
            ];
            $kecModel = new KecamatanModel();
            $target = $kecModel->where($where)->selectSum('target')->find();
            $data =  [
                'target' => $target[0]['target']
            ];
        }


        return $this->respond($data, 200);
    }

    public function barDrilldown()
    {
        $kab = $this->request->getPost('id_kab');

        // $kecModel = new KecamatanModel();
        $kabModel = new KabupatenModel();
        $dataKec = [];
        $drilldown = [];
        $drilldowns = [];
        $kabs = $kabModel->findAll();

        $keluargaModel = new KeluargaModel();

        foreach ($kabs as $kab) {
            // $keluargas = $keluargaModel->where(['id_kec' => $kec['id_kec']])->selectCount('no_kk')->select('id_desa, nama_desa')->groupBy('id_desa')->findAll();

            $drill = [
                'name' => $kab['nama_kab'],
                'id' => 'kab_' . $kab['id_kab']
            ];
            $jmlKab = 0;
            // foreach ($keluargas as $keluarga) {
            //     $drill['data'][] = [$keluarga['nama_desa'], (int) $keluarga['no_kk']];
            //     $jmlKec += (int) $keluarga['no_kk'];
            // }
            // $drilldown[] = $drill;

            // $desaModel = new DesaModel();
            $kecModel = new KecamatanModel();

            $kecs = $kecModel->where(['id_kab' => $kab['id_kab']])->findAll();
            foreach ($kecs as $kec) {
                $keluargas = $keluargaModel->where(['id_kec' => $kec['id_kec']])->selectCount('no_kk')->find();
                // $keluarga = $keluargas[0];

                $drills = [
                    'id' => 'kec_' . $kec['id_kec'],
                    'name' => $kec['nama_kec'],
                ];

                $desaModel = new DesaModel();
                $desas = $desaModel->where(['id_kec' => $kec['id_kec']])->findAll();
                foreach ($desas as $desa) {
                    $keluargas1 = $keluargaModel->where(['id_desa' => $desa['id_desa']])->selectCount('no_kk')->find();
                    $drills['data'][] = [
                        $desa['nama_desa'], (int) $keluargas1[0]['no_kk'],
                    ];
                }
                $drill['data'][] = [
                    'name' => $kec['nama_kec'],
                    'y' => (int) $keluargas[0]['no_kk'],
                    'drilldown' => 'kec_' . $kec['id_kec']
                ];

                $jmlKab += (int) $keluargas[0]['no_kk'];
                $drilldowns[] = $drills;
            }

            $drilldown[] = $drill;
            $dataKab[] = [
                'name'  => $kab['nama_kab'],
                'y' => $jmlKab,
                'drilldown'    => 'kab_' . $kab['id_kab']
            ];
        }

        $data = [
            'series' => $dataKab,
            // 'drilldown' => $drilldown
            'drilldown' => array_merge($drilldown, $drilldowns)
        ];

        return $this->respond($data, 200);
    }
}
