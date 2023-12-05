<?php

namespace music_matching_app\lib;

use Exception;
use music_matching_app\Bootstrap;
use music_matching_app\lib\SessionManager;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class Login
{
    private $db = [];

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function loginEmail($mail_address, $password)
    {
        $table = 'authentication';
        $column = '';
        $where = ' mail_address = ?';
        $arrVal = [$mail_address];

        $accountInfo = $this->db->select($table, $column, $where, $arrVal);
        try{
            if(count($accountInfo) === 0){
               throw new Exception('メールアドレスまたはパスワードが間違っています。');
            }
            if(password_verify($password, $accountInfo[0]['password_hash']) !== true){
                throw new Exception('メールアドレスまたはパスワードが間違っています。');
            }

            $key = 'member_id';
            $member_id = $accountInfo[0]['member_id'];
            $ses = new SessionManager($this->db);
            $ses->set($key, $member_id);
            header('Location: ' . Bootstrap::ENTRY_URL . 'eventlist.php');
            exit();
        }catch(Exception $e){
            echo $e->getMessage();
        }

    }
}
