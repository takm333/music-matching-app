<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class Token
{
    public static function createToken()
    {
        $token = hash('sha256',uniqid(rand(),1));
        return $token;
    }
}
