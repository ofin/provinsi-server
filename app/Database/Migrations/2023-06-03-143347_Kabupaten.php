<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Kabupaten extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_kab' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'auto_increment' => true,
            ],
            'nama_kab' => [
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
        $this->forge->addKey('id_kab', true);
        $this->forge->createTable('kabupaten');
    }

    public function down()
    {
        //
    }
}
