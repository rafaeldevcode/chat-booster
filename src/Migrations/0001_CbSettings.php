<?php

namespace ChatBooster\Migrations;

class CbSettings extends Migrate
{
    public $table = 'cb_settings';

    public function init()
    {
        $this->integer('id')->primaryKey();
        $this->string('name', 50);
        $this->string('avatar', 200)->default('https://upload.wikimedia.org/wikipedia/commons/1/1e/Default-avatar.jpg');
        $this->string('attach', 40)->nullable();
        $this->json('chat')->default('[]');

        $this->timestamps();

        $this->create();
    }
}
