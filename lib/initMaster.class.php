<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class initMaster
{
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

    public function initProfileList()
    {
        $genderArr = $this->createGenderArr();
        $ageArr = $this->createAgeArr();
        $areaArr = $this->createAreaArr();
        $genreArr = $this->createGenreArr();

        $initArr = [
            $genderArr,
            $ageArr,
            $areaArr,
            $genreArr
        ];
        return $initArr;
    }

    public function initParticipantStatusList()
    {
        $statusArr = $this->createStatusArr();
        return $statusArr;
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

    private function createStatusArr()
    {
        $table = 'participant_status';
        $column = 'status_id, status';
        $res = $this->db->select($table, $column);
        return $res;
    }

    private function createGenderArr()
    {
        $table = 'genders';
        $column = 'gender_id, gender';
        $res = $this->db->select($table, $column);
        return $res;
    }

    private function createAgeArr()
    {
        $table = 'ages';
        $column = 'age_id, age';
        $res = $this->db->select($table, $column);
        return $res;
    }

}
