<?php

namespace Yurun\OAuthLogin\Lib;

abstract class Base
{
    public static function parseKey($key)
    {
        return wordwrap(preg_replace('/[\r\n]/', '', $key), 64, "\n", true);
    }
}
