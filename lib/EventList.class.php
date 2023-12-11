<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class EventList{

    private $db = null;
    private $table = '';
    private $column = '';
    private $where = '';
    private $arrVal = [];
    private $joinArr = [];
    private $favoriteArr = [];
    private $onlyFavoriteArr = [];

    public function __construct($db,$member_id = '')
    {
        $this->db = $db;
        $this->table = 'events';
        $this->column = 'title, event_date, areas.area, venue';
        $this->where = '';
        $this->arrVal = [];

        $this->joinArr = [
            [
                'join_type' => ' JOIN ',
                'join' => 'areas',
                'on' => 'events.area_id = areas.area_id'
            ]
        ];

        $this->favoriteArr = [
            'join_type' => ' LEFT JOIN ',
            'join' => 'user_favorites',
            'on' => 'events.event_id = user_favorites.event_id'
        ];

        $this->onlyFavoriteArr = [
            'join_type' => ' JOIN ',
            'join' => 'user_favorites',
            'on' => 'events.event_id = user_favorites.event_id'
        ];

    }

    public function displayRecentEvent($member_id)
    {
        if($member_id !== ''){
            $this->getFavoriteColumn($member_id);
        }

        $this->db->setJoins($this->joinArr);

        $order = 'event_date asc';
        $this->db->setOrder($order);

        $res = $this->db->select($this->table, $this->column, $this->where, $this->arrVal);
        var_dump($res);
        return $res;
    }

    public function displayNewEvent($member_id)
    {
        if($member_id !== ''){
            $this->getFavoriteColumn($member_id);
        }

        $this->db->setJoins($this->joinArr);
        $order = 'events.created_at asc';
        $this->db->setOrder($order);

        $res = $this->db->select($this->table, $this->column, $this->where, $this->arrVal);
        var_dump($res);
        return $res;
    }

    public function displayFavoriteEvent($member_id)
    {
        if($member_id !== ''){
            $this->getOnlyFavoriteEvent($member_id);
        }

        $this->db->setJoins($this->joinArr);
        $order = 'events.created_at asc';
        $this->db->setOrder($order);

        $res = $this->db->select($this->table, $this->column, $this->where, $this->arrVal);
        var_dump($res);
        return $res;
    }

    private function getFavoriteColumn($member_id)
    {
        //ログインしている場合、お気に入り状況を表示
        $this->column .= ', user_favorites.is_favorite';
        $this->arrVal = [$member_id];
        $this->where = '( user_favorites.member_id IS NULL OR user_favorites.member_id = ? ) AND events.deleted_at IS null';
        array_push($this->joinArr, $this->favoriteArr);
    }

    private function getOnlyFavoriteEvent($member_id)
    {
        $this->arrVal = [$member_id];
        $this->where = ' user_favorites.is_favorite = 1 and user_favorites.member_id = ? ';
        array_push($this->joinArr, $this->onlyFavoriteArr);
    }

}
