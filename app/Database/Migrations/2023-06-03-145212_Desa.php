<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Desa extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_desa' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'auto_increment' => true,
            ],
            'id_kec' => [
                'type'           => 'INT',
                'constraint'     => 5,
            ],
            'nama_desa' => [
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
        $this->forge->addKey('id_desa', true);
        $this->forge->createTable('desa');
    }

    public function down()
    {
        //
    }
}
