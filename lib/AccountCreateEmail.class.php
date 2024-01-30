<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

use music_matching_app\Bootstrap;
use music_matching_app\lib\Mail;

class AccountCreateEmail
{
    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function registPreAccount($mail_address, $password)
    {
        //未登録
        $url = $this->createPreAccount($mail_address, $password, $this->db);
        $textUrl = Bootstrap::REGIST_EMAIL_TEXT_URL;
        $textNote = Bootstrap::REGIST_EMAIL_TEXT_NOTE;
        $text = $textUrl . $url . PHP_EOL .  $textNote;
        $subject = Bootstrap::REGIST_EMAIL_SUBJECT;
        $res = Mail::sendMail($mail_address, $subject, $text);

        return $res;
    }


    private function createPreAccount($mail_address, $password, $db)
    {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $url_token = TokenManager::createToken();

        $table = 'pre_users';
        $insData = [
            'mail_address' => $mail_address,
            'password_hash' => $password_hash,
            'url_token' => $url_token
        ];

        $res = $db->insert($table, $insData);
        $url = Bootstrap::ENTRY_URL . 'registAccount.php?url_token=' . $url_token;

        return $url;
    }

    public function registAccount($accountInfo)
    {
        //本会員登録
        $mail_address = $accountInfo['mail_address'];
        $password_hash = $accountInfo['password_hash'];

        $table = 'authentication';
        $insData = [
            'mail_address' => $mail_address,
            'password_hash' => $password_hash
        ];

        $res = $this->db->insert($table, $insData);
        $member_id = $this->db->getLastId();
        echo '登録完了!';

        //preテーブルの'is_registered'を1にする
        //登録済みとする
        $url_token = $accountInfo['url_token'];
        $table = 'pre_users';
        $where = ' url_token = ? ';
        $insData = [
            'is_registered' => 1
        ];
        $arrWhereVal = [$url_token];

        $res = $this->db->update($table, $where, $insData, $arrWhereVal);
        return $member_id;
    }
}
