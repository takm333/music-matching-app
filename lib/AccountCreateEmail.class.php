<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

use music_matching_app\Bootstrap;
use music_matching_app\lib\Mail;

class AccountCreateEmail{

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function registPreAccount($mail_address,$password){
        if($this->accountExists($mail_address)){
            //登録済み
        }else{
            //未登録
            $url = $this->createPreAccount($mail_address, $password, $this->db);

            $subject = 'test';
            Mail::sendMail($mail_address, $subject, $url);
        }
    }

    private function accountExists($mail_address)
    {
        $table = 'authentication';
        $where = ' mail_address = ? ';
        $arrVal = [$mail_address];
        $count = $this->db->count($table, $where, $arrVal);

        return boolval($count);
    }

    private function createPreAccount($mail_address, $password, $db){
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $url_token = TokenManager::createToken();

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
