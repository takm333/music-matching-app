<?php

namespace music_matching_app\lib;

use finfo;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class FileDownload
{
    public static function download($filePath, $mimeType = null)
    {
        if(! is_readable($filePath)) {
            exit();
        }

        // MIMEタイプの指定がない場合、自動判定
        if(! isset($mimeType)) {
            $finfo = new finfo();
            $mimeType = $finfo->file($filePath, FILEINFO_MIME_TYPE);
        }

        //適切なMIMEタイプが得られないときはapplication/octet-stream
        if (! preg_match('/\A\S+?\/\S+/', $mimeType)) {
            $mimeType = 'application/octet-stream';
        }

        header('Content-Type: ' . $mimeType);

        // ブラウザが実行不可能なMIMEタイプを実行可能なMIMEタイプに変換してしまう処理を抑止
        header('X-Content-Type-Options: nosniff');

        //ファイルサイズ
        header('Content-Length:' . filesize($filePath));

        //ファイル名
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');

        //keep-aliveの無効化
        header('Connection: close');

        readfile($filePath);
        exit();

    }
}
