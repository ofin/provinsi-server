<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Tim extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'username' => [
                'type'           => 'varchar',
                'constraint'     => '100',
            ],
            'nama' => [
                'type'           => 'varchar',
                'constraint'     => '100',
            ],
            'id_kab' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'nama_kab' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'id_kec' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'nama_kec' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'id_desa' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'nama_desa' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],


        ]);
        $this->forge->addKey('username', true);
        $this->forge->createTable('tim');
    }

    public function down()
    {
        //
    }
}
