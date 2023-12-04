<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

use music_matching_app\Bootstrap;

class PasswordChange
{
    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function checkCurrentPassword($currentPassword)
    {
        //パスワード変更時に確認する
    }

    public function changePassword($newPassword, $member_id)
    {
        $newPassword_hash = password_hash($newPassword,PASSWORD_DEFAULT);

        $table = 'authentication';
        $where = ' member_id = ? ';
        $insData = [
            'password_hash' => $newPassword_hash
        ];
        $arrWhereVal = [$member_id];

        $res = $this->db->update($table, $where, $insData, $arrWhereVal);

        //ログインする
    }
}
