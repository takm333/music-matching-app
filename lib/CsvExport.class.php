<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

class CsvExport
{
    public function exportCsv($csvType, $csvHeader, $csvArr)
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

        FileDownload::download($fileName, $mimeType);
    }

}
