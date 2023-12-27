<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

use Exception;
use music_matching_app\Bootstrap;
use music_matching_app\lib\SessionManager;

class Login
{
    private $db = [];

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function loginEmail($mail_address, $password)
    {
        $accountInfo = $this->searchAccount($mail_address, $password);
        try{
            $this->verifyAccountInfo($password, $accountInfo);
            $this->setSession($accountInfo);
            header('Location: ' . Bootstrap::ENTRY_URL . 'eventlist.php');
            exit();
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function firstLoginEmail($mail_address, $password)
    {
        $accountInfo = $this->searchAccount($mail_address, $password);
        try{
            $this->setSession($accountInfo);
            header('Location: ' . Bootstrap::ENTRY_URL . 'eventlist.php');
            exit();
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    private function searchAccount($mail_address, $password)
    {
        $table = 'authentication';
        $column = '';
        $where = ' mail_address = ?';
        $arrVal = [$mail_address];

        $accountInfo = $this->db->select($table, $column, $where, $arrVal);
        return $accountInfo;
    }

    private function verifyAccountInfo($password, $accountInfo){
        if(count($accountInfo) === 0){
            throw new Exception('メールアドレスまたはパスワードが間違っています。');
         }
         if(password_verify($password, $accountInfo[0]['password_hash']) !== true){
             throw new Exception('メールアドレスまたはパスワードが間違っています。');
         }
    }

    private function setSession($accountInfo){
        $key = 'member_id';
        $login_member_id = $accountInfo[0]['member_id'];

        $ses = new SessionManager($this->db);
        session_regenerate_id(TRUE);
        $ses->session_id = session_id();
        $ses->set($key, $login_member_id);
        $ses->insertSession($login_member_id);
    }

}
