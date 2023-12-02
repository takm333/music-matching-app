<?php

namespace music_matching_app\lib;

use music_matching_app\lib\Mail;
use music_matching_app\lib\PreAccountCreate;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

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
            $pre = new PreAccountCreate;
            $url = $pre->registPreAccount($mail_address, $password, $this->db);

            $type = 'regist';

            $mail = new Mail;
            $mail->sendMail($type, $mail_address, $url);
        }
    }

    private function accountExists($mail_address)
    {
        $table = 'authentication';
        $where = ' mail_address = ? ';
        $arrVal = [$mail_address];
        $count = $this->db->count($table, $where, $arrVal);

        echo $count;
        return boolval($count);

    }
}
