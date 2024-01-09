<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class Favorite
{
    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function countFavorite($event_id)
    {
        $table = 'user_favorites';
        $where = 'event_id = ? AND is_favorite = 1';
        $arrVal = [$event_id];

        $res = $this->db->count($table, $where, $arrVal);
        return $res;
    }

    public function checkUserFavorite($member_id, $event_id)
    {
        $table = 'user_favorites';
        $column = 'is_favorite';
        $where = 'member_id = ? AND event_id = ?';
        $arrWhereVal = [$member_id, $event_id];

        $favoriteArr = $this->db->select($table, $column, $where, $arrWhereVal);
        $is_favorite = (empty($favoriteArr) === false) ? $favoriteArr[0]['is_favorite'] : null;
        return $is_favorite;
    }


    public function insertFavorite($member_id, $event_id)
    {
        $table = 'user_favorites';
        $insData = [
            'member_id' => $member_id,
            'event_id' => $event_id,
            'is_favorite' => 1
        ];

        $res = $this->db->insert($table, $insData);
        return $res;
    }

    public function updateFavorite($member_id, $event_id, $current_is_favorite)
    {
        $update_is_favorite = ($current_is_favorite === 0) ? 1 : 0;

        $table = 'user_favorites';
        $where = 'member_id = ? AND event_id = ?';
        $arrWhereVal = [$member_id, $event_id];
        $insData = [
            'is_favorite' => $update_is_favorite
        ];

        $res = $this->db->update($table, $where, $insData, $arrWhereVal);
        return $res;
    }

    public function createJson($member_id, $event_id)
    {
        $favorites = $this->countFavorite($event_id);
        $isFavoriteUpdated = $this->checkUserFavorite($member_id, $event_id);
        $favoriteObj = [
        'favorites' => $favorites,
        'isFavoriteUpdated' => $isFavoriteUpdated
        ];
        return $favoriteObj;
    }
}
