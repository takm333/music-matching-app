<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

use music_matching_app\Bootstrap;
use music_matching_app\lib\TokenManager;

class PasswordReset
{
    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function resetPassword($mail_address)
    {
        $accountInfo = $this->accountSearch($mail_address);
        if(! empty($accountInfo)) {
            $password_reset_token = TokenManager::createToken();

            $table = 'password_reset';
            $insData = [
                'member_id' => $accountInfo[0]['member_id'],
                'mail_address' => $accountInfo[0]['mail_address'],
                'password_reset_token' => $password_reset_token
            ];

            $this->db->insert($table, $insData);

            $subject = 'reset';
            $url = Bootstrap::ENTRY_URL . 'passwordChange.php?password_reset_token=' . $password_reset_token;
            Mail::sendMail($mail_address, $subject, $url);
        }

    }

    private function accountSearch($mail_address)
    {
        $table = 'authentication';
        $column = '';
        $where = ' mail_address = ? ';
        $arrVal = [$mail_address];
        $accountInfo = $this->db->select($table, $column, $where, $arrVal);

        return $accountInfo;
    }

}
