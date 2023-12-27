<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class EventParticipants{

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function searchEventParticipants($member_id, $event_id)
    {
        $table = 'users';
        $column = 'icon, nickname, user_id, participant_status.status, genders.gender, ages.age';
        $where = '';
        $arrVal = [$member_id, $event_id];

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
}
