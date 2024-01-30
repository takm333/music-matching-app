<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class EventParticipants
{
    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function searchEventParticipants($member_id, $event_id, $participationStatus)
    {
        //マッチング対象のステータスIDを設定
        if($participationStatus === 1){
            $searchParticipationStatus = 1;
        }else if($participationStatus === 2){
            $searchParticipationStatus = 3;
        }else{
            $searchParticipationStatus = 2;
        }

        $table = 'users';
        $column = 'icon, nickname, user_id, participant_status.status, genders.gender, ages.age';
        $where = '';
        $arrVal = [$member_id, $event_id, $searchParticipationStatus];

        $joinArr = [];

        $participantsArr = [
            'join_type' => ' JOIN ',
            'join' => ' (SELECT
                            MAX(id) as max_id, member_id, status_id
                        FROM
                            event_participants
                        WHERE
                            member_id != ?
                        AND event_id = ?
                        AND status_id = ?
                        AND deleted_at is null
                        GROUP BY member_id
                        ORDER BY max_id DESC) as participants ',
            'on' => 'users.member_id = participants.member_id'
        ];

        $gendersArr = [
            'join_type' => ' JOIN ',
            'join' => ' genders ',
            'on' => 'users.gender_id = genders.gender_id'
        ];

        $agesArr = [
            'join_type' => ' JOIN ',
            'join' => ' ages ',
            'on' => 'users.age_id = ages.age_id'
        ];

        $participantStatusArr = [
            'join_type' => ' JOIN ',
            'join' => ' participant_status ',
            'on' => 'participants.status_id = participant_status.status_id'
        ];

        array_push($joinArr, $participantsArr, $gendersArr, $agesArr, $participantStatusArr);

        $this->db->setJoins($joinArr);
        $res = $this->db->select($table, $column, $where, $arrVal);
        $res = count($res) !== 0 ? $res : [];
        return $res;
    }

    public function getNumberOfParticipants($eventInfo)
    {
        $participantsArr = [];
        foreach($eventInfo as  $val){
            $participants = $this->countEventParticipants($val['event_id']);
            array_push($participantsArr, $participants);
        }
        return $participantsArr;
    }

    //イベント参加人数（管理画面用、ユーザー画面に流用する場合自分も含んだ人数になるため注意）
    public function countEventParticipants($event_id)
    {
        $table = 'event_participants';
        $where = 'id in (SELECT
                            MAX(id) as max_id
                        FROM
                            event_participants
                        WHERE
                            event_id = ?
                        GROUP BY member_id)
                  AND deleted_at is null';
        $arrVal = [$event_id];

        $participants = $this->db->count($table, $where, $arrVal);
        return $participants;
    }
}
