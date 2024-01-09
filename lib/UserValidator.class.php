<?php

namespace music_matching_app\lib;

use music_matching_app\Bootstrap;

class UserValidator
{
    private $db;
    private $dataArr = [];
    private $errArr = [];

    public const NICKNAME_MAX = 20;
    public const USER_ID_MAX = 50;
    public const SELF_INTRODUCTION_MAX = 500;

    public function __construct($db)
    {
        $this->db = $db;
    }

    //共通
    private function createErrorMessage()
    {
        foreach($this->dataArr as $key => $val) {
            $this->errArr[$key] = '';
        }
    }

    public function getErrFlg()
    {
        $errFlg = true;
        foreach($this->errArr as $key => $value) {
            if($value !== '') {
                $errFlg = false;
            }
        }
        return $errFlg;
    }

    //新規登録時
    public function checkError($dataArr)
    {
        $this->dataArr = $dataArr;
        $this->createErrorMessage();
        $this->mail_address_check();
        $this->password_check();

        return $this->errArr;
    }

    private function mail_address_check()
    {
        if($this->dataArr['mail_address'] === '') {
            $this->errArr['mail_address'] = 'メールアドレスを入力してください。';
        }
        if(preg_match('/^[a-zA-Z0-9_.+-]+@([a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9]*\.)+[a-zA-Z]{2,}$/', $this->dataArr['mail_address']) === 0) {
            $this->errArr['mail_address'] = '正しいメールアドレスを入力してください。';
        }
    }

    private function password_check()
    {
        if($this->dataArr['password'] === '') {
            $this->errArr['password'] = 'パスワードを入力してください。';
        }
    }

    public function accountExists($dataArr)
    {
        $table = 'authentication';
        $where = ' mail_address = ? ';
        $arrVal = [$dataArr['mail_address']];
        $count = $this->db->count($table, $where, $arrVal);
        if($count !== 0) {
            $this->errArr['mail_address'] = 'このメールアドレスは既に登録されています。';
        }
    }


    //プロフィール登録、編集時
    //対象：画像、ニックネーム、ユーザーID、自己紹介、ジャンル、性別、年代、住んでる地域
    public function profileCheckError($dataArr, $tmpImage)
    {
        $this->dataArr = $dataArr;
        $this->createErrorMessage();
        $this->checkImage($tmpImage);
        $this->checkNickname();
        $this->checkUserId();
        $this->checkSelfIntroduction();
        $this->checkGenre();
        $this->checkGender();
        $this->checkAge();
        $this->checkArea();

        return $this->errArr;

    }

    private function checkImage($tmpImage = '')
    {
        if($tmpImage !== '' && $tmpImage['error'] === 0 && $tmpImage['size'] !== 0) {
            // 画像の処理

            //対応している拡張子
            $extensionArr = [
                'image/jpeg',
                'image/png',
                'image/gif'
            ];

            $imageInfo = getimagesize($tmpImage['tmp_name']);
            if($imageInfo === false) {
                $this->errArr['image'] = '画像ファイルをアップロードしてください。';
            } else {
                $imageMime = $imageInfo['mime'];

                if($tmpImage['size'] > 10485760) {
                    $this->errArr['image'] = 'アップロードできる画像のサイズは、10MBまでです。';
                } elseif(! in_array($imageMime, $extensionArr)) {
                    $extension = str_replace('image/', ' .', implode('、', $extensionArr));
                    $this->errArr['image'] = 'アップロードできる画像の拡張子は' . $extension . 'のみです。';
                }
            }

        }
    }

    private function checkNickname()
    {
        if($this->dataArr['nickname'] === '') {
            $this->errArr['nickname'] = 'ニックネームを入力してください。';
        }
        if(mb_strlen($this->dataArr['nickname']) > self::NICKNAME_MAX) {
            $this->errArr['nickname'] = 'ニックネームは20文字以内で入力してください。';
        }
    }

    private function checkUserId()
    {
        if($this->dataArr['user_id'] === '') {
            $this->errArr['user_id'] = 'ユーザーIDを入力してください。';
        }
        if(mb_strlen($this->dataArr['user_id']) > self::USER_ID_MAX) {
            $this->errArr['user_id'] = 'ユーザーIDは50文字以内で入力してください。';
        }
        if(preg_match('/^[0-9a-zA-Z]*$/', $this->dataArr['user_id']) === 0) {
            $this->errArr['user_id'] = 'ユーザーIDは半角英数字で入力してください。';
        }
    }

    private function checkSelfIntroduction()
    {
        echo mb_strlen($this->dataArr['self_introduction']);
        if(mb_strlen($this->dataArr['self_introduction']) > self::SELF_INTRODUCTION_MAX) {
            $this->errArr['self_introduction'] = '自己紹介は500文字以内で入力してください。';
        }
    }

    private function checkGenre()
    {
        if(! isset($this->dataArr['genre'])) {
            $this->errArr['genre'] = 'ジャンルを選択してください。';
        } else {
            $inputGenreCheck = true;
            $table = 'genres';
            $column = 'genre_id';
            $genresArr = $this->db->select($table, $column);
            foreach($genresArr as $col => $val) {
                $checkGenreArr[] = $val['genre_id'];
            }
            foreach($this->dataArr['genre'] as $col => $val) {
                if(! in_array($val, $checkGenreArr)) {
                    $inputGenreCheck = false;
                };
            }
            //ユーザーの不正入力チェック(未登録のIDがないか)
            if($inputGenreCheck === false) {
                $this->errArr['genre'] = 'チェックボックス内のジャンルを選 択してください。';
            }

            //ユーザーの不正入力チェック(チェックの個数)
            $genreMax = $this->db->count($table);
            if(count($this->dataArr['genre']) > $genreMax) {
                $this->errArr['genre'] = 'チェックボックス内のジャンルを選 択してください。';
            }
        }
    }

    private function checkGender()
    {
        if($this->dataArr['gender'] === '') {
            $this->errArr['gender'] = '性別を選択してください。';
        }

        $inputGenderCheck = true;
        $table = 'genders';
        $column = 'gender_id';
        $gendersArr = $this->db->select($table, $column);
        foreach($gendersArr as $col => $val) {
            $checkGenderArr[] = $val['gender_id'];
        }
        if(! in_array($this->dataArr['gender'], $checkGenderArr)) {
            $inputGenderCheck = false;
        }
        //ユーザーの不正入力チェック(未登録のIDがないか)
        if($inputGenderCheck === false) {
            $this->errArr['gender'] = 'セレクトボックス内の性別を選択してください。';
        }
    }

    private function checkAge()
    {
        if($this->dataArr['age'] === '') {
            $this->errArr['age'] = '年代を選択してください。';
        }

        $inputAgeCheck = true;
        $table = 'ages';
        $column = 'age_id';
        $agesArr = $this->db->select($table, $column);
        foreach($agesArr as $col => $val) {
            $checkAgeArr[] = $val['age_id'];
        }
        if(! in_array($this->dataArr['age'], $checkAgeArr)) {
            $inputAgeCheck = false;
        }
        //ユーザーの不正入力チェック(未登録のIDがないか)
        if($inputAgeCheck === false) {
            $this->errArr['age'] = 'セレクトボックス内の年代を選択してください。';
        }
    }

    private function checkArea()
    {
        if($this->dataArr['area'] === '') {
            $this->errArr['area'] = '住んでる地域を選択してください。';
        }

        $inputAreaCheck = true;
        $table = 'areas';
        $column = 'area_id';
        $areasArr = $this->db->select($table, $column);
        foreach($areasArr as $col => $val) {
            $checkAreaArr[] = $val['area_id'];
        }
        if(! in_array($this->dataArr['area'], $checkAreaArr)) {
            $inputAreaCheck = false;
        }
        //ユーザーの不正入力チェック(未登録のIDがないか)
        if($inputAreaCheck === false) {
            $this->errArr['area'] = 'セレクトボックス内の住んでる地域を選択してください。';
        }
    }

}
