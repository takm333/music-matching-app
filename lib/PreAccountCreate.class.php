<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

use music_matching_app\Bootstrap;

class PreAccountCreate
{
    public function registPreAccount($mail_address, $password, $db){
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $url_token = hash('sha256',uniqid(rand(),1));

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
