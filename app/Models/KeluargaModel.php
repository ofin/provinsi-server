<?php

namespace App\Models;

use CodeIgniter\Model;

class KeluargaModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'keluarga';
    protected $primaryKey       = 'no_kk';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'no_kk',
        'nik_kepala',
        'nama_kepala',
        'id_kab',
        'nama_kab',
        'id_kec',
        'nama_kec',
        'id_desa',
        'nama_desa',
        'nama_dusun',
        'alamat',
        'pekerjaan',
        'no_hp',
        'jml_anggota',
        'lat',
        'longi',
        'pengguna_pdam',
        'pemakaian_pdam',
        'pln',
        'no_pln',
        'kelas_bpjs',
        'suku',
        'bantuan',
        'username',
        'nama_petugas',
        'deleted_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
