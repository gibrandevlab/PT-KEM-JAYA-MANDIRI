<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

class HistoryExcelExport
{
    /**
     * @param array<int, string> $headers
     * @param iterable<array<int, mixed>> $rows
     */
    public static function download(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows): void {
            $writeCell = static function (mixed $value): void {
                $value = (string) ($value ?? '');
                $type = is_numeric($value) && $value !== '' ? 'Number' : 'String';
                echo '<Cell><Data ss:Type="' . $type . '">' . htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</Data></Cell>';
            };

            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<?mso-application progid="Excel.Sheet"?>';
            echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"><Worksheet ss:Name="Riwayat"><Table><Row>';
            foreach ($headers as $header) {
                $writeCell($header);
            }
            echo '</Row>';

            foreach ($rows as $row) {
                echo '<Row>';
                foreach ($row as $value) {
                    $writeCell($value);
                }
                echo '</Row>';
            }

            echo '</Table></Worksheet></Workbook>';
        }, $filename, ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8']);
    }
}
