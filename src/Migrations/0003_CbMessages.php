<?php

namespace ChatBooster\Migrations;

use ChatBooster\Enums\BooleanEnum;
use ChatBooster\Enums\MessageTypeEnum;

class CbMessages extends Migrate
{
    public $table = 'cb_messages';

    public function init()
    {
        $this->integer('id')->primaryKey();
        $this->text('message');
        $this->integer('type')->default(MessageTypeEnum::TEXT);
        $this->integer('from');
        $this->integer('to');
        $this->boolean('viewed')->default(BooleanEnum::FALSE);

        $this->timestamps();

        $this->create();
    }
}
