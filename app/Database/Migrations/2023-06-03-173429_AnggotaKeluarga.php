<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AnggotaKeluarga extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'no_kk' => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],
            'nik' => [
                'type'           => 'INT',
                'constraint'     => 25,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'no_urut' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ]


        ]);
        $this->forge->addKey('nik', true);
        $this->forge->createTable('anggota_keluarga');
    }

    public function down()
    {
        //
    }
}
