<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';
use DateTime;

class TokenManager
{

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public static function createToken()
    {
        $token = hash('sha256',uniqid(rand(),1));
        return $token;
    }

    public function tokenExists($token, $token_type)
    {
        $table = '';
        $column = '';
        $where = '';
        $arrVal = '';

        switch($token_type){
            case 'account_regist':
                $table = 'pre_users';
                $column = '';
                $where = ' url_token = ? ';
                $arrVal = [$token];
                break;

            case 'password_reset':
                $table = 'password_reset';
                $column = '';
                $where = ' password_reset_token = ? ';
                $arrVal = [$token];
                break;
        }

        return $accountInfo = $this->db->select($table, $column ,$where, $arrVal);
    }

    public function verifyToken($flag, $expiration_date)
    {
        $res['is_verify'] = false;

        $now = new DateTime('now');

        if($flag !== 0){
            $res['error'] = 'updated';
        }else if($now > $expiration_date){
            $res['error'] = 'expired';
        }else{
            $res['is_verify'] = true;
        }
        return $res;
    }
}
