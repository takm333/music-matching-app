<?php

namespace music_matching_app\lib;

use music_matching_app\Bootstrap;

class Recaptcha
{
    private $recap_response = null;

    public function __construct()
    {
        $this->recap_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='. Bootstrap::RECAPTCHA_SECRET_KEY . '&response=' . $_POST['g-recaptcha-response']);
    }

    public function checkBot()
    {
        $recap_response = json_decode($this->recap_response);
        if($recap_response->success === false) {
            return false;
        }
        return true;
    }
}
