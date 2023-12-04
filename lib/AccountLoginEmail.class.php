<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class AccountLoginEmail
{
    private $db = [];

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function login($mail_address, $password)
    {
        $table = 'authentication';
        $column = '';
        $where = ' mail_address = ?';
        $arrVal = [$mail_address];

        $res = $this->db->select($table, $column, $where, $arrVal);
        if(count($res) === 0){
            echo 'アカウントがない';
        }else if(password_verify($password, $res[0]['password_hash']) === false){
            echo 'パスワードミス';
        }else{
            echo 'ok';
        }
        var_dump($res);
    }
}
