<?php

namespace ChatBooster\Seeders;

use ChatBooster\Enums\RolesEnum;
use ChatBooster\Models\CbLead;
use ChatBooster\Models\CbSetting;

class Seed
{
    static public function init()
    {
        $settings = new CbSetting();

        if($settings->count() === 0):
            $settings->create([
                'name' => 'Admin',
            ]);
        endif;

        $lead = new CbLead();
        $lead->create([
            'name' => "Admin",
            'role' => RolesEnum::ADMIN,
        ]);

        for ($i = 0; $i < 20; $i++):
            $lead->create([
                'name' => "Usuário {$i}",
            ]);
        endfor;
    }
}