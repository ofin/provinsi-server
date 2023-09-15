<?php

namespace App\Controllers\Tim;

use App\Controllers\BaseController;
use App\Database\Migrations\AnggotaKeluarga;
use App\Models\AnggotaKeluargaModel;
use App\Models\KeluargaModel;
use CodeIgniter\API\ResponseTrait;
use Throwable;

class Pendataan extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $userData = $this->getUserData();

        $tanggal = $this->request->getPost('tgl');
        $semua = $this->request->getPost('semua');

        $keluargaModel = new KeluargaModel();
        if ($semua == 'false') {
            $where['date(created_at)'] = $tanggal;
        }
        $where['username'] = $userData->username;
        $data = $keluargaModel->where($where)->orderBy('created_at', 'desc')->findAll();

        return $this->respond($data, 200);
    }

    public function detail()
    {
        $id = $this->request->getPost('id');

        $keluargaModel = new KeluargaModel();

        $data = $keluargaModel->where(['no_kk' => $id])->orderBy('created_at', 'desc')->findAll();

        return $this->respond($data, 200);
    }

    public function insert()
    {
        helper('filesystem');
        $userData = $this->getUserData();

        $image = \Config\Services::image();

        $post = $this->request->getPost();
        if ($imagefile = $this->request->getFiles()) {
            $no = 0;
            $validation = [
                'src.*' => [
                    'rules'     => 'mime_in[src,image/jpg,image/jpeg]'
                ]
            ];
            if ($this->validate($validation)) {
                foreach ($imagefile['src'] as $img) {

                    if ($img->isValid() && !$img->hasMoved()) {
                        if (!is_dir('uploads/' . $post['no_kk'])) {
                            mkdir('uploads/' . $post['no_kk']);
                        }
                        $ext = $img->getClientExtension();
                        $newName = $no . '.' . $ext;
                        $image->withFile($img)
                            ->save(FCPATH . 'uploads/' . $post['no_kk'] . '/' . $newName, 50);
                        $no++;
                    }
                }
            } else {
                return $this->fail(['msg' => 'Dokumentasi harus file gambar']);
            }
        }

        $post['bantuan'] = implode(",", $post['bantuan']);
        $post['username'] = $userData->username;
        $post['nama_petugas'] = $userData->nama;

        $agg = $this->request->getPost('anggota');
        unset($post['anggota']);

        $keluargaModel = new KeluargaModel();
        $jml = $post['jml_anggota'] > 0 ? count($agg) / 2 : 0;

        $post['jml_anggota'] = $post['jml_anggota'] + 1;
        $kepala = [
            'nik' => $post['nik_kepala'],
            'nama' => $post['nama_kepala'],
            'no_kk' => $post['no_kk'],
            'status' => 'Kepala Keluarga',
            'no_urut' => 0
        ];

        $anggotaModel = new AnggotaKeluargaModel();
        $anggotaGagal = [];

        $cekKepala = $anggotaModel->find($kepala['nik']);
        if ($cekKepala) {
            return $this->fail([
                'msg'   => 'Kepala Keluarga sudah terdata di keluarga lain'
            ]);
        } else {
            $cekKepala = $anggotaModel->onlyDeleted()->find($kepala['nik']);
            if ($cekKepala) {
                $kepala['deleted_at'] = null;
                $anggotaModel->update($kepala['nik'], $kepala);
            } else {
                $anggotaModel->insert($kepala);
            }
        }

        $i = 0;

        while ($i < $jml) {
            $anggotas = [];
            foreach ($agg as $key => $value) {
                if ($key == 'nik_anggota_[' . $i) {
                    $anggotas['nik'] = $value;
                }
                if ($key == 'nama_anggota_[' . $i) {
                    $anggotas['nama'] = $value;
                }
            }
            $anggotas['no_urut'] = $i + 1;
            $anggotas['no_kk'] = $post['no_kk'];
            $anggotas['status'] = 'Anggota Keluarga';

            $cekAnggota = $anggotaModel->find($anggotas['nik']);

            if ($cekAnggota) {
                $anggotaGagal[] = [
                    $anggotas['nik']
                ];
                $post['jml_anggota'] -= 1;
            } else {
                $cekDeleted = $anggotaModel->onlyDeleted()->find($anggotas['nik']);
                if ($cekDeleted) {
                    $anggotas['deleted_at'] = null;
                    $anggotaModel->update($anggotas['nik'], $anggotas);
                } else {
                    $anggotaModel->insert($anggotas);
                }
            }
            $i ++;
        }

        // for ($i = 0; $i < $jml; $i++) {
        //     $anggotas = [];
        //     foreach ($agg as $key => $value) {
        //         if ($key == 'nik_anggota_[' . $i) {
        //             $anggotas['nik'] = $value;
        //         }
        //         if ($key == 'nama_anggota_[' . $i) {
        //             $anggotas['nama'] = $value;
        //         }
        //     }
        //     $anggotas['no_urut'] = $i + 1;
        //     $anggotas['no_kk'] = $post['no_kk'];
        //     $anggotas['status'] = 'Anggota Keluarga';

        //     $cekAnggota = $anggotaModel->find($anggotas['nik']);

        //     if ($cekAnggota) {
        //         $anggotaGagal[] = [
        //             $anggotas['nik']
        //         ];
        //         $post['jml_anggota'] -= 1;
        //     } else {
        //         $cekDeleted = $anggotaModel->onlyDeleted()->find($anggotas['nik']);
        //         if ($cekDeleted) {
        //             $anggotas['deleted_at'] = null;
        //             $anggotaModel->update($anggotas['nik'], $anggotas);
        //         }
        //         else{
        //             $anggotaModel->insert($anggotas);
        //         }
        //     }
        // }

        $cekKeluarga = $keluargaModel->find($post['no_kk']);

        if ($cekKeluarga) {
            $anggotaModel->where(['no_kk' => $post['no_kk']])->delete();
            return $this->fail([
                'msg'   => 'Nomor KK sudah ada'
            ]);
        } else {
            $cekKeluarga = $keluargaModel->onlyDeleted()->find($post['no_kk']);
            if ($cekKeluarga) {
                $post['deleted_at'] = null;
                $keluargaModel->update($post['no_kk'], $post);
                $affectedRows = $keluargaModel->affectedRows();
                if (!$affectedRows) {
                    return $this->fail([
                        'msg'   => 'Data tidak tersimpan'
                    ]);
                }
            } else {
                $keluargaModel->insert($post);
                $affectedRows = $keluargaModel->affectedRows();
                if (!$affectedRows) {
                    return $this->fail([
                        'msg'   => 'Data tidak tersimpan'
                    ]);
                }
            }
        }

        return $this->respond([
            'msg'   => 'Data Tersimpan',
            'anggotaGagal' => $anggotaGagal
        ], 200);

        // $map = directory_map(FCPATH . 'uploads/' . $post['no_kk'], false, false);
    }

    public function delete()
    {
        $id = $this->request->getPost('no_kk');

        $keluargaModel = new KeluargaModel();

        $keluargaModel->delete($id);
        $affectedRows = $keluargaModel->affectedRows();
        if (!$affectedRows) {
            return $this->fail('Data tidak terhapus');
        }
        $anggotaModel = new AnggotaKeluargaModel();
        $anggotaModel->where('no_kk', $id)->delete();
        $affectedRowsz = $anggotaModel->affectedRows();
        if (!$affectedRowsz) {
            return $this->fail('Data tidak terhapus');
        }
        return $this->respond($affectedRows . ' Data Terhapus', 200);

        // return $this->respond($data, 200);
    }
}
