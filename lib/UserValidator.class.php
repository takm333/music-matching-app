<?php

namespace music_matching_app\lib;

use music_matching_app\Bootstrap;

class UserValidator{

    private $dataArr = [];
    private $errArr = [];

    public function checkError($dataArr)
    {
        $this->dataArr = $dataArr;
        $this->createErrorMessage();
        $this->mail_address_check();
        $this->password_check();

        return $this->errArr;
    }

    private function createErrorMessage()
    {
        foreach($this->dataArr as $key => $val){
            $this->errArr[$key] = '';
        }
    }

    private function mail_address_check(){
        if($this->dataArr['mail_address'] === ''){
            $this->errArr['mail_address'] = 'メールアドレスを入力してください。';
        }
        if(preg_match('/^[a-zA-Z0-9_.+-]+@([a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9]*\.)+[a-zA-Z]{2,}$/', $this->dataArr['mail_address']) === 0){
            $this->errArr['mail_address'] = '正しいメールアドレスを入力してください。';
        }
    }

    private function password_check(){
        if($this->dataArr['password'] === ''){
            $this->errArr['password'] = 'パスワードを入力してください。';
        }
    }

    public function getErrFlg()
    {
        $errFlg = true;
        foreach($this->errArr as $key => $value){
            if($value !== ''){
                $errFlg = false;
            }
        }
        return $errFlg;
    }
}
