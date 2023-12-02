<?php

namespace music_matching_app\lib;

use DateTime;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class AccountVerifier
{
    private $db = null;
    private $accountInfo = [];

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function verifyAccount($url_token)
    {
         //期限確認、整合性確認
        //存在するか→登録済みじゃないか→期限内のトークンか→登録
        $table = 'pre_users';
        $column = '';
        $where = ' url_token = ? ';
        $arrVal = [$url_token];

        $is_verify = false;

        $this->accountInfo = $this->db->select($table, $column ,$where, $arrVal);
        $is_registered = $this->accountInfo[0]['is_registered'];
        $now = new DateTime('now');
        $expiration_date = new DateTime($this->accountInfo[0]['expiration_date']);

        if($is_registered !== 0){
            echo '登録済！';
        }else if($now > $expiration_date){
            //期限切れ
            echo '期限切れ';
        }else{
            echo '登録！';
            $is_verify = true;
        }

        return $is_verify;
    }

    public function registAccount()
    {
        //本会員登録
        $mail_address = $this->accountInfo[0]['mail_address'];
        $password_hash = $this->accountInfo[0]['password_hash'];

        $table = 'authentication';
        $insData = [
            'mail_address' => $mail_address,
            'password_hash' => $password_hash
        ];

        $res = $this->db->insert($table, $insData);
        echo '登録完了!';

        //preテーブルの'is_registered'を1にする
        //登録済みとする
        $url_token = $this->accountInfo[0]['url_token'];
        $table = 'pre_users';
        $where = ' url_token = ? ';
        $insData = [
            'is_registered' => 1
        ];
        $arrWhereVal = [$url_token];

        $res = $this->db->update($table, $where, $insData, $arrWhereVal);
    }
}
