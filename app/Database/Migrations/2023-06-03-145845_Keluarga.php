<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Keluarga extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'no_kk' => [
                'type'           => 'VARCHAR',
                'constraint'     => 25,
            ],
            'nik_kepala' => [
                'type'           => 'VARCHAR',
                'constraint'     => 25,
            ],
            'nama_kepala' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'id_kab' => [
                'type'       => 'INT',
                'constraint' => 5,
            ],
            'nama_kab' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'id_kec' => [
                'type'       => 'INT',
                'constraint' => 5,
            ],
            'nama_kec' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'id_desa' => [
                'type'       => 'INT',
                'constraint' => 5,
            ],
            'nama_desa' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'nama_dusun' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'alamat' => [
                'type'       => 'text',
            ],
            'pekerjaan' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'jml_anggota' => [
                'type'       => 'text',

            ],
            'lat' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'longi' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'pengguna_pdam' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'pemakaian_pdam' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'pln' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'no_pln' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'kelas_bpjs' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'suku' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'bantuan' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'username' => [
                'type'           => 'varchar',
                'constraint'     => '100',
            ],
            'nama_petugas' => [
                'type'           => 'varchar',
                'constraint'     => '100',
            ],
            'created_at' => [
                'type'       => 'datetime',
            ],
            'updated_at' => [
                'type'       => 'datetime',
            ],
            'deleted_at' => [
                'type'      => 'datetime',
                'null'      => true
            ],


        ]);
        $this->forge->addKey('no_kk', true);
        $this->forge->createTable('keluarga');
    }

    public function down()
    {
        //
    }
}
