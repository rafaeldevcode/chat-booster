<?php

namespace ChatBooster;

class View
{
    private static $setting = null;
    private static $chat = null;

    public static function render()
    {
        require __DIR__.'/../resources/partials/buttons.php';

        if(!is_null(self::$setting)):
            require self::$setting;
        endif;

        if(!is_null(self::$chat)):
            require self::$chat;
        endif;
    }

    public static function chat(): self
    {
        self::$chat = __DIR__.'/../resources/chat.php';

        return new static;
    }

    public static function setting(): self
    {
        self::$setting = __DIR__.'/../resources/setting.php';

        return new static;
    }
}