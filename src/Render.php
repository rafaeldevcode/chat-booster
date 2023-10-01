<?php

namespace ChatBooster;

class Render
{
    public static function chat(): void
    {
        require __DIR__.'/../resources/chat.php';
    }

    public static function setting(): void
    {
        require __DIR__.'/../resources/setting.php';
    }
}