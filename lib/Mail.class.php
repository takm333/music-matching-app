<?php

namespace music_matching_app\lib;

use music_matching_app\Bootstrap;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class Mail{

    public function sendMail($type, $mail_address, $param = '')
    {
        switch($type){
            case 'regist':
                $to = $mail_address;
                $subject = 'test';
                $message = $param;
                $headers = 'From: '. Bootstrap::FROM_MAIL_ADDRESS . "\r\n";

                $res = mb_send_mail($to, $subject, $message, $headers);
                break;
        }
    }
}
