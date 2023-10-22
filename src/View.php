<?php

namespace ChatBooster;

use ChatBooster\Models\CbLead;
use ChatBooster\Models\CbSetting;

class View
{
    private static $setting = null;
    private static $chat = null;
    private static $has_migrations = [];
    private static $has_file_setting = true;
    private static $leads = [];
    private static $settings = [];

    public static function render()
    {
        self::$has_file_setting = Setting::hasFileSettings();
        self::$has_migrations = Setting::hasMigrations();
        self::setLeads();
        self::setSettings();

        self::require(__DIR__.'/../resources/partials/buttons');

        if(!is_null(self::$setting)):
            self::require(self::$setting);
        endif;

        if(!is_null(self::$chat)):
            self::require(self::$chat);
        endif;
    }

    public static function chat(): self
    {
        self::$chat = __DIR__.'/../resources/chat';

        return new static;
    }

    public static function setting(): self
    {
        self::$setting = __DIR__.'/../resources/setting';

        return new static;
    }

    public static function require(string $path, array $data = []): void
    {
        extract($data);

        $path = substr($path, -4) == '.php' ? $path : "{$path}.php";

        require $path;
    }

    private static function setLeads()
    {
        if(self::$has_file_setting && empty(self::$has_migrations)):
            $leads = new CbLead();
            self::$leads = $leads->paginate(15, 'updated_at');
        endif;
    }

    private static function setSettings()
    {
        if(self::$has_file_setting && empty(self::$has_migrations)):
            $settings = new CbSetting();
            self::$settings = $settings->first();
        endif;
    }
}