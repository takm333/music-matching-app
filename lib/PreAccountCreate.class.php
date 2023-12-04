<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

use music_matching_app\Bootstrap;
use music_matching_app\lib\Token;

class PreAccountCreate
{
    public function createPreAccount($mail_address, $password, $db){
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $url_token = Token::createToken();

        $table = 'pre_users';
        $insData = [
            'mail_address' => $mail_address,
            'password_hash' => $password_hash,
            'url_token' => $url_token
        ];

        $res = $db->insert($table, $insData);
        $url = Bootstrap::ENTRY_URL . 'registAccount.php?url_token=' . $url_token;

        return $url;
    }
}
