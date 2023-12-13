<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class initMaster{

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function initEventList()
    {
        $genreArr = $this->createGenreArr();
        $areaArr = $this->createAreaArr();

        $initArr = [
            $genreArr,
            $areaArr
        ];
        return $initArr;
    }

    private function createGenreArr()
    {
        $table = 'genres';
        $column = 'genre_id, genre';
        $res = $this->db->select($table, $column);
        return $res;
    }

    private function createAreaArr()
    {
        $table = 'areas';
        $column = 'area_id, area';
        $res = $this->db->select($table, $column);
        return $res;
    }
}
