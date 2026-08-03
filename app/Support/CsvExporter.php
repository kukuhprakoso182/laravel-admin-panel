<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter
{
    /**
     * @param  Collection  $rows       Data yang mau di-export (hasil ->get())
     * @param  array       $headers    Header kolom CSV, misal ['Nama', 'Email', ...]
     * @param  callable    $mapRow     fn($row): array — ubah 1 row jadi array sesuai urutan $headers
     * @param  string      $filenamePrefix  Prefix nama file, misal 'users'
     */
    public static function stream(Collection $rows, array $headers, callable $mapRow, string $filenamePrefix): StreamedResponse
    {
        $filename = $filenamePrefix . '-' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($rows, $headers, $mapRow) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $mapRow($row));
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
