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
    private $areasArr = [];
    private $favoriteArr = [];
    private $onlyFavoriteArr = [];
    private $artistsArr = [];
    private $genresArr = [];

    public function __construct($db)
    {
        $this->db = $db;
        $this->table = 'events';
        $this->column = 'title, event_date, areas_table.area, venue';
        $this->where = '';
        $this->arrVal = [];

        $this->areasArr = [
            'join_type' => ' JOIN ',
            'join' => 'areas as areas_table ',
            'on' => 'events.area_id = areas_table.area_id'
        ];

        $this->favoriteArr = [
            'join_type' => ' LEFT JOIN ',
            'join' => 'user_favorites as user_favorites_table',
            'on' => 'events.event_id = user_favorites_table.event_id'
        ];

        $this->onlyFavoriteArr = [
            'join_type' => ' JOIN ',
            'join' => 'user_favorites as user_favorites_table',
            'on' => 'events.event_id = user_favorites_table.event_id'
        ];

        $this->artistsArr = [
            'join_type' => ' JOIN ',
            'join' => 'event_artists as event_artists_table',
            'on' => 'events.event_id = event_artists_table.event_id'
        ];

        $this->genresArr = [
            'join_type' => ' JOIN ',
            'join' => 'event_genres as event_genres_table',
            'on' => 'events.event_id = event_genres_table.event_id'
        ];

    }

    public function displayRecentEvent($member_id, $searchArr = [])
    {
        if($member_id !== ''){
            $this->getFavoriteColumn($member_id);
        }

        if($searchArr !== []){
            $this->setCondition($searchArr, $member_id);
        }else{
            $this->joinArr []= $this->areasArr;
            $this->db->setJoins($this->joinArr);
        }

        $order = 'event_date asc';
        $this->db->setOrder($order);

        $res = $this->db->select($this->table, $this->column, $this->where, $this->arrVal);
        return $res;
    }

    public function displayNewEvent($member_id, $searchArr = [])
    {
        if($member_id !== ''){
            $this->getFavoriteColumn($member_id);
        }

        if($searchArr !== []){
            $this->setCondition($searchArr, $member_id);
        }else{
            $this->joinArr []= $this->areasArr;
            $this->db->setJoins($this->joinArr);
        }

        $order = 'events.created_at desc';
        $this->db->setOrder($order);

        $res = $this->db->select($this->table, $this->column, $this->where, $this->arrVal);
        return $res;
    }

    public function displayFavoriteEvent($member_id, $searchArr = [])
    {
        if($member_id !== ''){
            $this->getOnlyFavoriteEvent($member_id);
        }

        if($searchArr !== []){
            $this->setCondition($searchArr, $member_id);
        }else{
            $this->joinArr []= $this->areasArr;
            $this->db->setJoins($this->joinArr);
        }
        $order = 'event_date asc';
        $this->db->setOrder($order);

        $res = $this->db->select($this->table, $this->column, $this->where, $this->arrVal);
        return $res;
    }

    public function setCondition($searchArr, $member_id = '')
    {
        $this->createWhere($searchArr);
        array_push($this->joinArr, $this->artistsArr, $this->genresArr, $this->areasArr);
        if($member_id === ''){
            array_push($this->joinArr, $this->favoriteArr);
        }
        $this->db->setJoins($this->joinArr);
        $groupby = 'events.event_id';
        $this->db->setGroupBy($groupby);
    }

    private function getFavoriteColumn($member_id)
    {
        //ログインしている場合、お気に入り状況を表示
        $this->column .= ', user_favorites_table.is_favorite';
        $this->arrVal = [$member_id];
        $this->where = '( user_favorites_table.member_id IS NULL OR user_favorites_table.member_id = ? ) AND events.deleted_at IS null';
        array_push($this->joinArr, $this->favoriteArr);
    }

    private function getOnlyFavoriteEvent($member_id)
    {
        $this->column .= ', user_favorites_table.is_favorite';
        $this->arrVal = [$member_id];
        $this->where = ' user_favorites_table.is_favorite = 1 and user_favorites_table.member_id = ? ';
        array_push($this->joinArr, $this->onlyFavoriteArr);
    }


    private function createWhere($searchArr)
    {
        foreach($searchArr as $col => $val){
            if($val !== ""){
                //name属性から関数名を作って実行
                $createCondition = 'create' . $col;
                if($this->where !== ''){
                    $this->where .= ' AND ';
                }
                $this->$createCondition($val);
            }
        }
    }

    private function createSearchBox($val)
    {
        $searchBox = '(title LIKE ? OR event_artists_table.artist_name LIKE ? OR venue LIKE ? ) ';
        $preCnt = mb_substr_count($searchBox, '?');

        var_dump($val);
        $this->where .= $searchBox;
        $i = 0;
        while($i < $preCnt){
            $this->arrVal []= '%' . $val . '%';
            $i++;
        }
        var_dump($this->arrVal);
    }

    private function createArea($val)
    {
        $area = '(events.area_id = ? ) ';

        $this->where .= $area;
        $this->arrVal []= $val;
    }

    private function createGenre($val)
    {
        if($val[0] !== '0'){
            $genre = '( event_genres_table.genre_id';
            $this->where .= $genre;

            $preCnt = count($val);
            if($preCnt === 1){
                $this->where .= ' = ? ) ';
                $this->arrVal []= $val[0];
            }else{
                $this->where .= ' in (';
                $commaCnt = $preCnt - 1;
                $i = 0;
                while($i < $preCnt){
                    $this->where .= ($i < $commaCnt) ?  ' ?,' : ' ?';
                    $this->arrVal []= $val[$i];
                    $i ++;
                }
                $this->where .= ' ) ) ';
            }
        }
    }

    private function createBetween($val)
    {
        $from = ($val[0] !== '') ? date('Y-m-d H:i:s', strtotime($val[0])) : '0000-00-00 00:00:00';
        $to = ($val[1] !== '') ? date('Y-m-d H:i:s', strtotime($val[1] . ' 23:59:59')) : '9999-12-31 23:59:59';

        $between = 'events.event_date BETWEEN ? AND ? ';
        $this->where .= $between;
        array_push($this->arrVal, $from, $to);
    }
}
