<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Login extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'username' => [
                'type'           => 'varchar',
                'constraint'     => '100',

            ],
            'password' => [
                'type'           => 'text',
            ],
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => '1',
            ],


        ]);
        $this->forge->addKey('username', true);
        $this->forge->createTable('login');
    }

    public function down()
    {
        //
    }
}
