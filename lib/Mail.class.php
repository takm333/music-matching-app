<?php

namespace music_matching_app\lib;

use music_matching_app\Bootstrap;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class Mail
{
    public static function sendMail($to, $subject, $text = '')
    {
        $headers = 'From: '. Bootstrap::FROM_MAIL_ADDRESS . "\r\n";
        return mb_send_mail($to, $subject, $text, $headers);
    }
}
