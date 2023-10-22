<?php

namespace ChatBooster;

use ChatBooster\Migrations\Migrate;

class Setting
{
    public static function hasMigrations(): array
    {
        if(!self::hasFileSettings()) return [];

        $not_migration = [];

        foreach(self::getCurretsTables() as $table):
            $migration = new Migrate();

            $prefix = substr($table, 0, 5);
            $table = str_replace($prefix, '', $table);

            if(!$migration->hasTable($table)):
                $not_migration[] = $prefix.$table;
            endif;
        endforeach;

       return $not_migration;
    }

    public static function hasFileSettings(): bool
    {
        return file_exists(__DIR__.'/../../../../chatbooster.settings.php');
    }

    public static function sortByCreatedAt($a, $b){
        return strtotime($a['created_at']) - strtotime($b['created_at']);
    }

    private static function getCurretsTables(): array
    {
        return [
            "0001_cb_settings",
            "0002_cb_leads",
            '0003_cb_messages',
        ];
    }
}