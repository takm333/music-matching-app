<?php

namespace music_matching_app\lib;

use DateTime;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class TokenVerifier
{
    private $db = null;

    public function verifyToken($token)
    {
        $table = 'password_reset';
        $column = '';
        $where = ' password_reset_token = ? ';
        $arrVal = [$token];

        $is_verify = false;

        $accountInfo = $this->db->select($table, $column ,$where, $arrVal);
        if(empty($accountInfo)){
            echo '無効なページ';
            exit();
        }
        $is_reset = $accountInfo[0]['is_reset'];
        $member_id = $accountInfo[0]['member_id'];
        $now = new DateTime('now');
        $expiration_date = new DateTime($accountInfo[0]['expiration_date']);

        if($is_reset !== 0){
            echo '登録済！';
        }else if($now > $expiration_date){
            //期限切れ
            echo '期限切れ';
        }else{
            echo '登録！';
            $is_verify = true;
        }

        $res = [
                'is_verify' => $is_verify,
                'member_id' => $member_id
        ];

        return $res;
    }
}
