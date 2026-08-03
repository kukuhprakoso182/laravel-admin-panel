<?php

namespace Tests\Unit\Support;

use App\Support\CsvExporter;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Catatan: pakai Tests\TestCase (bukan PHPUnit\Framework\TestCase murni)
 * karena CsvExporter memanggil helper response()->streamDownload(), yang
 * butuh service container Laravel sudah ter-boot.
 */
class CsvExporterTest extends TestCase
{
    protected function captureStreamedCsv($response): string
    {
        ob_start();
        $response->sendContent();
        return ob_get_clean();
    }

    public function test_stream_menghasilkan_header_dan_baris_data_sesuai_mapping(): void
    {
        $rows = new Collection([
            (object) ['name' => 'Budi', 'email' => 'budi@example.com'],
            (object) ['name' => 'Siti', 'email' => 'siti@example.com'],
        ]);

        $response = CsvExporter::stream(
            rows: $rows,
            headers: ['Nama', 'Email'],
            mapRow: fn ($row) => [$row->name, $row->email],
            filenamePrefix: 'test-export',
        );

        $csv = $this->captureStreamedCsv($response);

        $this->assertStringContainsString('Nama,Email', str_replace("\r\n", "\n", $csv));
        $this->assertStringContainsString('Budi,budi@example.com', $csv);
        $this->assertStringContainsString('Siti,siti@example.com', $csv);
    }

    public function test_stream_dengan_collection_kosong_tetap_menghasilkan_header_saja(): void
    {
        $response = CsvExporter::stream(
            rows: new Collection(),
            headers: ['Kolom A', 'Kolom B'],
            mapRow: fn ($row) => [],
            filenamePrefix: 'kosong',
        );

        $csv = $this->captureStreamedCsv($response);

        $this->assertStringContainsString('Kolom A,Kolom B', str_replace("\r\n", "\n", $csv));
    }

    public function test_nama_file_mengandung_prefix_dan_ekstensi_csv(): void
    {
        $response = CsvExporter::stream(
            rows: new Collection(),
            headers: ['A'],
            mapRow: fn ($row) => [],
            filenamePrefix: 'users',
        );

        $disposition = $response->headers->get('Content-Disposition');

        $this->assertStringContainsString('users-', $disposition);
        $this->assertStringContainsString('.csv', $disposition);
    }

    public function test_content_type_adalah_text_csv(): void
    {
        $response = CsvExporter::stream(
            rows: new Collection(),
            headers: ['A'],
            mapRow: fn ($row) => [],
            filenamePrefix: 'test',
        );

        $this->assertSame('text/csv', $response->headers->get('Content-Type'));
    }

    public function test_nilai_yang_mengandung_koma_di_quote_otomatis_oleh_fputcsv(): void
    {
        $rows = new Collection([
            (object) ['label' => 'Jakarta, Indonesia'],
        ]);

        $response = CsvExporter::stream(
            rows: $rows,
            headers: ['Lokasi'],
            mapRow: fn ($row) => [$row->label],
            filenamePrefix: 'lokasi',
        );

        $csv = $this->captureStreamedCsv($response);

        $this->assertStringContainsString('"Jakarta, Indonesia"', $csv);
    }
}
