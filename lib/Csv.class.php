<?php

namespace music_matching_app\lib;

use DateTime;
use SplFileObject;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class Csv
{
    private $db = null;
    private $errArr = [];

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function exportCsv($csvType, $csvHeader, $csvArr = [])
    {
        $csvDir = dirname(__FILE__) . '/../admin/csv';
        if(! file_exists($csvDir)) {
            mkdir($csvDir, 0777);
        }
        $fileName = $csvDir . '/' . $csvType . '_' . date('Ymd_His') . '_' . uniqid()  .'.csv';
        $mimeType = 'text/csv';

        array_unshift($csvArr, $csvHeader);
        touch($fileName);
        $fp = fopen($fileName, 'w');
        foreach ($csvArr as $value) {
            fputcsv($fp, $value);
        }

        File::download($fileName, $mimeType);
    }

    //csvインポート
    public function importEventCsv($filePath)
    {
        [$csvAssocArr,$errLineArr] = $this->convertCsv($filePath);
        if(count($errLineArr) === 0){
            if($this->isCsvImportable($csvAssocArr)){
                return $this->insertCsv($csvAssocArr);
            }
        }else{
            foreach($errLineArr as $lineNumber){
                $this->errArr[$lineNumber]['element'] = $lineNumber . '行目の形式が正しくありません。正しくカンマで区切られているか確認してください。';
            }
        }
        return json_encode($this->errArr);
    }


    //csvを連想配列に変換
    private function convertCsv($filePath)
    {
        $csv = new SplFileObject($filePath);
        $csv->setFlags(SplFileObject::READ_CSV);

        $keys = ['title','image','open_time','start_time','area_id','venue','link','admin_id','artist_name','genre_id', 'ticket_name', 'ticket_price'];

        $lineNumber = 0;
        $csvAssocArr = [];
        $errLineArr = [];
        foreach($csv as $key => $line){
            //行数カウント
            //1行目、データがない列は処理しない
            $lineNumber++;
            if($lineNumber === 1 || $line[0] === null) continue;


            if(count($keys) !== count($line)){
                $errLineArr[] = $lineNumber;
            }else{
                $lineAssocArr = array_combine($keys, $line);
                $lineAssocArr['artist_name'] = explode(',', $lineAssocArr['artist_name']);
                $lineAssocArr['genre_id'] = explode(',', $lineAssocArr['genre_id']);
                $lineAssocArr['ticket_name'] = explode(',', $lineAssocArr['ticket_name']);
                $lineAssocArr['ticket_price'] = explode(',', $lineAssocArr['ticket_price']);
                $lineAssocArr['tickets'] = array_combine($lineAssocArr['ticket_name'], $lineAssocArr['ticket_price']);
                array_push($csvAssocArr, $lineAssocArr);
            }
        }
        return [$csvAssocArr,$errLineArr];
    }


    //インポートしたcsvにエラーがないか判定
    private function isCsvImportable($csvAssocArr)
    {
        //csvの入力欄が2行目以降のため、2としている
        $lineNumber = 2;
        foreach($csvAssocArr as $row){
            $this->checkTitle($row, $lineNumber);
            $this->checkImage($row, $lineNumber);
            $this->checkOpenTime($row, $lineNumber);
            $this->checkStartTime($row, $lineNumber);
            $this->checkAreaId($row, $lineNumber);
            $this->checkVenue($row, $lineNumber);
            $this->checkLink($row, $lineNumber);
            $this->checkAdminId($row, $lineNumber);
            $this->checkArtistName($row, $lineNumber);
            $this->checkGenreId($row, $lineNumber);
            $this->checkTicketName($row, $lineNumber);
            $this->checkTicketPrice($row, $lineNumber);
            $lineNumber++;
        }
        return $this->isImportable();
    }

    private function checkTitle($row, $lineNumber)
    {
        if($row['title'] === ''){
            $this->errArr[$lineNumber]['title'] = $lineNumber . '行目のタイトル列を入力してください。';
        }
        if(mb_strlen($row['title']) > 255){
            $this->errArr[$lineNumber]['title'] = $lineNumber . '行目のタイトル列は255文字以下で入力してください。';
        }
    }

    private function checkImage($row, $lineNumber)
    {
        if(mb_strlen($row['image']) > 255){
            $this->errArr[$lineNumber]['image'] = $lineNumber . '行目の画像列は255文字以下で入力してください。';
        }
    }

    private function checkOpenTime($row, $lineNumber)
    {
        if($row['open_time'] === ''){
            $this->errArr[$lineNumber]['open_time'] = $lineNumber . '行目の開場時刻列を入力してください。';
        }
        if($this->validateDate($row['open_time']) === false){
            $this->errArr[$lineNumber]['open_time'] = $lineNumber . '行目の開場時刻列に正しい日時を入力してください。';
        }
        if($this->validateDate($row['open_time']) && date($row['open_time']) < date('Y-m-d H:i:s')){
            $this->errArr[$lineNumber]['open_time'] = $lineNumber . '行目の開場時刻列に未来の日時を入力してください。';
        }
    }

    private function checkStartTime($row, $lineNumber)
    {
        if($row['start_time'] === ''){
            $this->errArr[$lineNumber]['start_time'] = $lineNumber . '行目の開演時刻列を入力してください。';
        }
        if($this->validateDate($row['start_time']) === false){
            $this->errArr[$lineNumber]['start_time'] = $lineNumber . '行目の開演時刻列に正しい日時を入力してください。';
        }
        if($this->validateDate($row['start_time']) && date($row['open_time']) < date('Y-m-d H:i:s')){
            $this->errArr[$lineNumber]['start_time'] = $lineNumber . '行目の開演時刻列に未来の日時を入力してください。';
        }
    }

    //日付の妥当性、フォーマット確認
    private function validateDate($date, $format='Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    private function checkAreaId($row, $lineNumber)
    {
        if($row['area_id'] === ''){
            $this->errArr[$lineNumber]['area_id'] = $lineNumber . '行目のエリアID列を入力してください。';
        }
        //文字列型の整数or0か判定(csvから連想配列にする際、文字列になるため)
        if(!ctype_digit($row['area_id']) || $row['area_id'] === '0'){
            $this->errArr[$lineNumber]['area_id'] = $lineNumber . '行目のエリアID列は正の整数を入力してください。';
        }else{
            $table = 'areas';
            $where = 'area_id = ?';
            $arrVal = [$row['area_id']];

            $count = $this->db->count($table, $where, $arrVal);

            if($count === 0){
                $this->errArr[$lineNumber]['area_id'] = $lineNumber . '行目のエリアID列に正しいエリアIDを入力してください。';
            }
        }
    }

    private function checkVenue($row, $lineNumber)
    {
        if($row['venue'] === ''){
            $this->errArr[$lineNumber]['venue'] = $lineNumber . '行目の会場列を入力してください。';
        }
        if(mb_strlen($row['venue']) > 255){
            $this->errArr[$lineNumber]['venue'] = $lineNumber . '行目の会場列は255文字以下で入力してください。';
        }
    }

    private function checkLink($row, $lineNumber)
    {
        if($row['link'] === ''){
            $this->errArr[$lineNumber]['link'] = $lineNumber . '行目のリンク列を入力してください。';
        }
    }

    private function checkAdminId($row, $lineNumber)
    {
        if($row['admin_id'] === ''){
            $this->errArr[$lineNumber]['admin_id'] = $lineNumber . '行目の管理者ID列を入力してください。';
        }
        //文字列型の整数or0か判定(csvから連想配列にする際、文字列になるため)
        if(!ctype_digit($row['admin_id']) || $row['admin_id'] === '0'){
            $this->errArr[$lineNumber]['admin_id'] = $lineNumber . '行目の管理者ID列は正の整数を入力してください。';
        }else{
            $table = 'admins';
            $where = 'admin_id = ?';
            $arrVal = [$row['admin_id']];

            $count = $this->db->count($table, $where, $arrVal);

            if($count === 0){
                $this->errArr[$lineNumber]['admin_id'] = $lineNumber . '行目の管理者ID列に正しい管理者IDを入力してください。';
            }
        }
    }

    private function checkArtistName($row, $lineNumber)
    {
        foreach($row['artist_name'] as $artistName){
            if($artistName === ''){
                $this->errArr[$lineNumber]['artist_name'] = $lineNumber . '行目のアーティスト名列を入力してください。';
            }
            if(mb_strlen($artistName) > 300){
                $this->errArr[$lineNumber]['artist_name'] = $lineNumber . '行目のアーティスト名列は300文字以下で入力してください。';
            }
        }
    }

    private function checkGenreId($row, $lineNumber)
    {
        foreach($row['genre_id'] as $GenreId){
            if($row['genre_id'] === ''){
                $this->errArr[$lineNumber]['genre_id'] = $lineNumber . '行目のジャンルID列を入力してください。';
            }
            if(!ctype_digit($GenreId) || $GenreId === '0'){
                $this->errArr[$lineNumber]['genre_id'] = $lineNumber . '行目のジャンルID列は正の整数を入力してください。';
            }else{
                $table = 'genres';
                $where = 'genre_id = ?';
                $arrVal = [$GenreId];

                $count = $this->db->count($table, $where, $arrVal);

                if($count === 0){
                    $this->errArr[$lineNumber]['genre_id'] = $lineNumber . '行目のジャンルID列に正しいジャンルIDを入力してください。';
                }
            }
        }
    }

    private function checkTicketName($row, $lineNumber)
    {
        foreach($row['ticket_name'] as $TicketName){
            if($TicketName === ''){
                $this->errArr[$lineNumber]['ticket_name'] = $lineNumber . '行目のチケット名列を入力してください。';
            }
            if(mb_strlen($TicketName) > 255){
                $this->errArr[$lineNumber]['ticket_name'] = $lineNumber . '行目のチケット名列は255文字以下で入力してください。';
            }
        }
    }

    private function checkTicketPrice($row, $lineNumber)
    {
        foreach($row['ticket_price'] as $TicketPrice){
            if($TicketPrice === ''){
                $this->errArr[$lineNumber]['ticket_price'] = $lineNumber . '行目の価格列を入力してください。';
            }
            if(!ctype_digit($TicketPrice) || $TicketPrice === '0'){
                $this->errArr[$lineNumber]['ticket_price'] = $lineNumber . '行目の価格列は正の整数を入力してください。';
            }
        }
    }

    private function isImportable()
    {
        if(count($this->errArr) === 0){
            return true;
        }else{
            return false;
        }
    }

    private function insertCsv($csvAssocArr)
    {
        $res = true;

        foreach($csvAssocArr as $line){
            $table = 'events';
            $insData = [
                'title' => $line['title'],
                'image' => $line['image'],
                'open_time' => $line['open_time'],
                'start_time' => $line['start_time'],
                'area_id' => $line['area_id'],
                'venue' => $line['venue'],
                'link' => $line['link'],
                'admin_id' => $line['admin_id']
            ];
            $res = $this->db->insert($table, $insData);
            $eventId = $this->db->getLastId();

            foreach($line['artist_name'] as $artist){
                $table = 'event_artists';
                $insData = [
                    'event_id' => $eventId,
                    'artist_name' => $artist,
                    'admin_id' => $line['admin_id']
                ];

                $this->db->insert($table, $insData);
            }

            foreach($line['genre_id'] as $genreId){
                $table = 'event_genres';
                $insData = [
                    'event_id' => $eventId,
                    'genre_id' => $genreId,
                    'admin_id' => $line['admin_id']
                ];

                $this->db->insert($table, $insData);
            }

            foreach($line['tickets'] as $ticketName => $ticketPrice){
                $table = 'event_prices';
                $insData = [
                    'event_id' => $eventId,
                    'ticket_name' => $ticketName,
                    'price' => $ticketPrice
                ];

                $this->db->insert($table, $insData);
            }
        }
    return $res;
    }
}
