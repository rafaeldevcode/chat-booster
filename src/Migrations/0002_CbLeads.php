<?php

namespace ChatBooster\Migrations;

use ChatBooster\Enums\RolesEnum;

class CbLeads extends Migrate
{
    public $table = 'cb_leads';

    public function init()
    {
        $this->integer('id')->primaryKey();
        $this->string('name', 150)->nullable();
        $this->string('email', 150)->nullable();
        $this->string('phone', 20)->nullable();
        $this->string('date_of_birth', 10)->nullable();
        $this->string('avatar', 200)->default('https://upload.wikimedia.org/wikipedia/commons/1/1e/Default-avatar.jpg');
        $this->integer('role')->default(RolesEnum::USER);
        $this->integer('position')->default(0);
        $this->integer('level')->default(0);

        $this->timestamps();

        $this->create();
    }
}
