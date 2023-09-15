<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Kecamatan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_kec' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'auto_increment' => true,
            ],
            'id_kab' => [
                'type'           => 'INT',
                'constraint'     => 5,
            ],
            'nama_kec' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'jml_dpt' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'jml_masyarakat' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'jml_kk' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'target' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type'       => 'datetime',
            ],
            'updated_at' => [
                'type'       => 'datetime',
            ],
            'deleted_at' => [
                'type'       => 'datetime',
            ],


        ]);
        $this->forge->addKey('id_kec', true);
        $this->forge->createTable('kecamatan');
    }

    public function down()
    {
        //
    }
}
