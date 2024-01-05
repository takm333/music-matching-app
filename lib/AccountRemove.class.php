<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class AccountRemove{

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    //アカウント削除
    //管理機能追加後、削除した管理者IDの更新機能を追加する
    public function removeUserAccount($member_id)
    {
        $table = 'authentication';
        $where = ' member_id = ? ';
        $insData = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];
        $arrWhereVal = [$member_id];

        $res = $this->db->update($table, $where, $insData, $arrWhereVal);
        return $res;
    }
}
