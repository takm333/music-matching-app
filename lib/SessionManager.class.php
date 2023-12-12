<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class SessionManager
{
    public $db = null;
    public $session_id = '';

    public function __construct($db)
    {
        if(isset($_SESSION) === false) session_start();


        $this->session_id = session_id();

        $this->db = $db;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public function destroy()
    {
        session_destroy();
    }

    public function checkSession()
    {
        $member_id = $this->selectSession();
        if($member_id !== false){
            $_SESSION['member_id'] = $member_id;
        // }else{
            //セッションIDがない(初めてこのサイトに来ている)
            // $res = $this->insertSession($login_member_id);
            // if($res === true){
            //     //最後にAuto incrementで登録したIDを$_SESSION
            //     $_SESSION['member_id'] = $this->db->getLastId();
            // }else{
            //     $_SESSION['member_id'] = '';
            // }
        }
    }

    public function selectSession()
    {
        $table = ' user_sessions ';
        $col = ' member_id ';
        $where = ' session_id = ? AND deleted_at IS NULL';
        $arrVal = [$this->session_id];

        $res = $this->db->select($table, $col, $where, $arrVal);
        //var_dump($res);
        //sqlの実行結果は配列で返ってくるので$res[0]を指定
        return (count($res) !== 0) ? $res[0]['member_id'] : false;
    }

    public function insertSession($login_member_id)
    {
        $table = ' user_sessions ';
        $insData = [
            'member_id' => $login_member_id,
            'session_id ' => $this->session_id
        ];
        $res = $this->db->insert($table, $insData);
        return $res;
    }

    public function deleteSession()
    {
        $table = ' user_sessions ';
        $where = ' session_id = ? ';
        $insData = [
            ' deleted_at ' => date('Y-m-d H:i:s')
        ];
        $arrWhereVal = [$this->session_id];

        $res = $this->db->update($table, $where, $insData, $arrWhereVal);
        return $res;
    }
}
