<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class Event{

    private $db = null;
    private $from = '0000-00-00 00:00:00';
    private $to = '9999-12-31 23:59:59';
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
    private $eventGenresArr = [];
    private $priceArr = [];
    private $dowArr = ['日', '月', '火', '水', '木', '金', '土'];

    public function __construct($db)
    {
        $this->db = $db;
        $this->table = 'events';
        $this->column = 'events.event_id, title, image, open_time, areas_table.area, venue';
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
            'on' => 'events.event_id = user_favorites_table.event_id AND user_favorites_table.member_id = ?'
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
            'join' => 'genres as genres_table',
            'on' => 'event_genres_table.genre_id = genres_table.genre_id'
        ];

        $this->eventGenresArr = [
            'join_type' => ' JOIN ',
            'join' => 'event_genres as event_genres_table',
            'on' => 'events.event_id = event_genres_table.event_id'
        ];

        $this->priceArr = [
            'join_type' => ' JOIN ',
            'join' => 'event_prices as event_prices_table',
            'on' => 'events.event_id = event_prices_table.event_id'
        ];

    }

    //初期表示、直近のライブ
    public function displayRecentEventList($member_id, $searchArr = [])
    {
        if($member_id !== ''){
            $this->setFavoriteColumn($member_id);
        }

        if($searchArr !== []){
            $this->setCondition($searchArr, $member_id);
        }else{
            $this->joinArr []= $this->areasArr;
            $this->db->setJoins($this->joinArr);
        }

        $order = 'open_time asc';
        $this->db->setOrder($order);

        $res = $this->db->select($this->table, $this->column, $this->where, $this->arrVal);
        return $res;
    }

    //新着のライブ
    public function displayNewEventList($member_id, $searchArr = [])
    {
        if($member_id !== ''){
            $this->setFavoriteColumn($member_id);
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

    //お気に入り
    public function displayFavoriteEventList($member_id, $searchArr = [])
    {
        if($member_id !== ''){
            $this->setOnlyFavoriteColumn($member_id);
        }

        if($searchArr !== []){
            $this->setCondition($searchArr, $member_id);
        }else{
            $this->joinArr []= $this->areasArr;
            $this->db->setJoins($this->joinArr);
        }
        $order = 'open_time asc';
        $this->db->setOrder($order);

        $res = $this->db->select($this->table, $this->column, $this->where, $this->arrVal);
        return $res;
    }

    //イベント詳細
    public function displayDetailEvent($event_id, $member_id = '')
    {
        if($member_id !== ''){
            $this->setFavoriteColumn($member_id);
        }

        $this->setEventDetailColumn($event_id);
        $this->db->setJoins($this->joinArr);

        $res = $this->db->select($this->table, $this->column, $this->where, $this->arrVal);

        $eventDetail = $this->formatEventDetail($res);
        return $eventDetail;
    }


    //ログイン時お気に入り状況取得
    private function setFavoriteColumn($member_id)
    {
        $this->column .= ', user_favorites_table.is_favorite';
        $this->arrVal = [$member_id];
        $this->where = 'events.deleted_at IS null';
        array_push($this->joinArr, $this->favoriteArr);
    }

    //お気に入りのみ取得
    private function setOnlyFavoriteColumn($member_id)
    {
        $this->column .= ', user_favorites_table.is_favorite';
        $this->arrVal = [$member_id];
        $this->where = ' user_favorites_table.is_favorite = 1 and user_favorites_table.member_id = ? ';
        array_push($this->joinArr, $this->onlyFavoriteArr);
    }

    //検索条件指定
    private function setCondition($searchArr, $member_id = '')
    {
        $this->createWhere($searchArr);
        array_push($this->joinArr, $this->artistsArr, $this->eventGenresArr, $this->areasArr);
        // if($member_id !== ''){
        //     array_push($this->joinArr, $this->favoriteArr);
        // }
        $this->db->setJoins($this->joinArr);
        $groupby = 'events.event_id';
        $this->db->setGroupBy($groupby);
    }

    //イベント詳細
    private function setEventDetailColumn($event_id)
    {
        $this->setAnd();

        $this->column .= ', start_time, link, event_prices_table.ticket_name,event_prices_table.price, event_artists_table.artist_name, genres_table.genre';
        $this->arrVal []= $event_id;
        $this->where .= ' events.event_id = ? ';
        array_push($this->joinArr, $this->artistsArr, $this->eventGenresArr,$this->genresArr,  $this->areasArr, $this->priceArr);
    }

    private function formatEventDetail($res)
    {
        $eventDetail = $res[0];
        $eventDetail['event_date'] = date('Y/n/d', strtotime($eventDetail['open_time']));
        $eventDetail['event_date_ja'] = date('Y年n月d日', strtotime($eventDetail['open_time']));
        $eventDetail['dow'] = $this->dowArr[date('w', strtotime($eventDetail['open_time']))];
        $eventDetail['open_time'] = date('G:i', strtotime($eventDetail['open_time']));
        $eventDetail['start_time'] = date('G:i', strtotime($eventDetail['start_time']));
        $eventDetail['ticket_name'] = [];
        $eventDetail['price'] = [];
        $eventDetail['artist_name'] =  [];
        $eventDetail['genre'] =  [];

        foreach($res as $row){
            if(!in_array($row['ticket_name'], $eventDetail['ticket_name'])){
                $eventDetail['ticket_name'] []= $row['ticket_name'];
            }
            if(!in_array($row['price'], $eventDetail['price'])){
                $eventDetail['price'] []= $row['price'];
            }
            if(!in_array($row['artist_name'], $eventDetail['artist_name'])){
                $eventDetail['artist_name'] []= $row['artist_name'];
            }
            if(!in_array($row['genre'], $eventDetail['genre'])){
                $eventDetail['genre'] []= $row['genre'];
            }
        }
        return $eventDetail;
    }

    private function createWhere($searchArr)
    {
        foreach($searchArr as $col => $val){
            if($val !== ""){
                //name属性から関数名を作って実行
                $createCondition = 'create' . $col;
                $this->$createCondition($val);
            }
        }
        $this->createBetween($this->from, $this->to);
    }

    private function createSearchBox($val)
    {
        $this->setAnd();

        $searchBox = '(title LIKE ? OR event_artists_table.artist_name LIKE ? OR venue LIKE ? ) ';
        $preCnt = mb_substr_count($searchBox, '?');

        $this->where .= $searchBox;
        $i = 0;
        while($i < $preCnt){
            $this->arrVal []= '%' . $val . '%';
            $i++;
        }
    }

    private function createArea($val)
    {
        $this->setAnd();

        $area = '(events.area_id = ? ) ';

        $this->where .= $area;
        $this->arrVal []= $val;
    }

    private function createGenre($val)
    {
        if($val[0] !== '0'){
            $this->setAnd();

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

    private function createFrom($val)
    {
        $this->from =  date('Y-m-d H:i:s', strtotime($val));
    }

    private function createTo($val)
    {
        $this->to = date('Y-m-d H:i:s', strtotime($val . ' 23:59:59'));
    }

    private function createBetween($from, $to)
    {

        $this->setAnd();

        $between = 'events.open_time BETWEEN ? AND ? ';
        $this->where .= $between;
        array_push($this->arrVal, $from, $to);
    }

    private function setAnd()
    {
        if($this->where !== ''){
            $this->where .= ' AND ';
        }
    }

}
