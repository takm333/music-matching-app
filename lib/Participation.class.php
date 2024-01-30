<?php

namespace music_matching_app\lib;

class Participation
{
    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    //ステータス確認
    public function searchParticipationStatus($member_id, $event_id)
    {
        $table = 'event_participants';
        $column = '';
        $where = 'member_id = ? AND event_id = ?';
        $arrVal = [$member_id, $event_id];
        $order = 'event_participants.id desc';
        $limit = 1;

        $this->db->setOrder($order);
        $this->db->setLimitOff($limit, $order = '');

        $res = $this->db->select($table, $column, $where, $arrVal);
        if(!empty($res)) {
            if($res[0]['deleted_at'] === null) {
                $participationStatus = $res[0]['status_id'];
            } else {
                //キャンセル済
                $participationStatus = 99;
            }
        } else {
            //未参加
            $participationStatus = '';
        }
        return $participationStatus;
    }

    public function insertParticipationStatus($member_id, $event_id, $status)
    {
        if($this->isUpdatableStatus($event_id)){
            $table = 'event_participants';
            $insData = [
                'member_id' => $member_id,
                'event_id' => $event_id,
                'status_id' => $status
            ];

            $res = $this->db->insert($table, $insData);
            return $res;
        }
    }

    public function cancelParticipation($member_id, $event_id)
    {
        if($this->isUpdatableStatus($event_id)){
            $table = ' event_participants ';
            $where = ' id = (SELECT MAX(id) FROM event_participants WHERE member_id = ? AND event_id = ? ) ';
            $insData = [
                ' deleted_at ' => date('Y-m-d H:i:s')
            ];

            $arrWhereVal = [$member_id, $event_id];
            $res = $this->db->update($table, $where, $insData, $arrWhereVal);
            return $res;
        }
    }

    //開始時刻と現在時刻を比較、開始時刻を過ぎている場合falseを返しステータスを更新させない
    private function isUpdatableStatus($event_id)
    {
        $table = 'events';
        $column = 'open_time';
        $where = 'event_id = ?';
        $arrVal = [$event_id];

        $res = $this->db->select($table, $column, $where, $arrVal);

        $openTime = $res[0]['open_time'];
        $now = date('Y-m-d H:i:s');

        return ($openTime < $now) ? false : true;
    }

}
