<?php

namespace music_matching_app\lib;

class Profile{

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function searchMyProfile($member_id)
    {
        $res = $this->selectMyProfile($member_id);
        $profile = $this->formatProfile($res);

        return $profile;
    }

    public function searchUserProfile($nickname)
    {

    }

    public function searchParticipateEvent($member_id, $than)
    {
        $res = $this->selectParticipateEvent($member_id, $than);
        return $res;
    }

    public function updateProfile($member_id, $profile, $imageName = '')
    {
        $this->updateUsers($member_id, $profile, $imageName);

        [$deletedArr, $insertArr] = $this->createUsersGenreUpdateData($member_id, $profile);

        if(!empty($deletedArr)){
            $this->deleteUsersGenre($member_id, $deletedArr);
        }
        if(!empty($insertArr)){
            $this->insertUsersGenre($member_id, $insertArr);
        }

    }

    private function updateUsers($member_id, $profile, $imageName = '')
    {
        $table = 'users';
        $where = 'member_id = ?';
        $insData = [
            'user_id' => $profile['user_id'],
            'nickname' => $profile['nickname'],
            'self_introduction' => $profile['self_introduction'],
            'gender_id' => $profile['gender'],
            'age_id' => $profile['age'],
            'area_id' => $profile['area']
        ];
        // 画像空ではない場合、更新する
        if($imageName !== ''){
            $insData['icon'] = $imageName;
        }
        $arrWhereVal = [$member_id];

        $res = $this->db->update($table, $where, $insData, $arrWhereVal);

        return $res;
    }

    private function createUsersGenreUpdateData($member_id, $profile)
    {

        $table = 'user_genres';
        $column = 'genre_id';
        $where = 'member_id = ? AND deleted_at IS NULL';
        $arrVal = [$member_id];

        $beforeGenreArr = $this->db->select($table, $column, $where, $arrVal);
        foreach($beforeGenreArr as $key => $val){
            $before[] = $val['genre_id'];
        }

        foreach($profile['genre'] as $key => $val){
            $after[] = $val;
        }

        $diff_before = array_diff($before, $after);//beforeにだけ存在する
        $diff_after = array_diff($after, $before);//afterにだけ存在する
        $diff = array_merge($diff_before, $diff_after);

        $deletedArr = [];
        $insertArr = [];
        foreach($diff as $key => $val){
            if(in_array($val, $before)){
                $deletedArr[] = $val;
            }else{
                $insertArr[] = $val;
            }
        }

        return [$deletedArr, $insertArr];
    }

    private function deleteUsersGenre($member_id, $deletedArr)
    {
        $preSt = implode(',', array_fill(0, count($deletedArr), '?'));

        $table = 'user_genres';
        $insData = [
            'deleted_at' => date('Y-m-d H:i:s')
        ] ;
        $where = 'member_id = ? AND genre_id in ( ' . $preSt . ' )';
        $arrVal = array_merge([$member_id], $deletedArr);

        $this->db->update($table, $where, $insData, $arrVal);
    }

    private function insertUsersGenre($member_id, $insertArr)
    {
        $table = 'user_genres';
        foreach($insertArr as $key => $val){
            $insData = [
                'member_id' => $member_id,
                'genre_id' => $val
            ];

        $this->db->insert($table, $insData);
        }
    }

    private function selectParticipateEvent($member_id, $than)
    {
        $table = 'events';
        $column = ' events.event_id, title, image, open_time, areas.area, venue, user_favorites.is_favorite';
        $where = 'events.event_id IN (SELECT  participants_A.event_id  FROM event_participants as participants_A JOIN  events  ON participants_A.event_id = events.event_id JOIN
        (SELECT event_id,  max(id) as max_id
        FROM
            event_participants
        WHERE
            member_id = ?
        GROUP BY
            event_id) AS  participants_B  ON participants_A.event_id = participants_B.event_id
            AND participants_A.id = participants_B.max_id WHERE  events.open_time ' . $than . ' now() AND participants_A.deleted_at is NULL)';
        $arrVal = [$member_id, $member_id,];

        $joinArr = [];

        $areasArr = [
            'join_type' => ' JOIN ',
            'join' => 'areas',
            'on' => 'events.area_id = areas.area_id'
        ];

        $favoriteArr = [
            'join_type' => ' LEFT JOIN ',
            'join' => 'user_favorites',
            'on' => 'events.event_id = user_favorites.event_id AND user_favorites.member_id = ?'
        ];

        array_push($joinArr, $areasArr, $favoriteArr);

        $this->db->setJoins($joinArr);
        $res = $this->db->select($table, $column, $where, $arrVal);
        return $res;

    }

    private function selectMyProfile($member_id)
    {
        $table = 'users';
        $column = 'users.member_id, user_id, nickname, icon, self_introduction, genres.genre, genders.gender, ages.age, areas.area';
        $where = 'users.member_id = ? AND user_genres.deleted_at IS NULL';
        $arrVal = [$member_id];

        $joinArr = [];

        $userGenresArr = [
            'join_type' => ' LEFT JOIN ',
            'join' => ' user_genres ',
            'on' => 'users.member_id = user_genres.member_id '
        ];

        $genresArr = [
            'join_type' => ' LEFT JOIN ',
            'join' => ' genres ',
            'on' => 'user_genres.genre_id = genres.genre_id'
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

        $areasArr = [
            'join_type' => ' JOIN ',
            'join' => ' areas ',
            'on' => 'users.area_id = areas.area_id'
        ];

        array_push($joinArr, $userGenresArr, $genresArr,  $gendersArr, $agesArr, $areasArr);

        $this->db->setJoins($joinArr);
        $res = $this->db->select($table, $column, $where, $arrVal);

        return $res;
    }

    private function formatProfile($res)
    {
        if(empty($res)){
            $profile = [];
            return $profile;
        }
        $profile = $res[0];
        $profile['genre'] = [];

        foreach($res as $row){
            $profile['genre'][] = $row['genre'];
        }

        return $profile;
    }
}
