<?php

namespace Tests\Unit\Support;

use App\Support\TableResponseFormatter;
use Illuminate\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\TestCase;

class TableResponseFormatterTest extends TestCase
{
    protected function makePaginator(array $items, int $total, int $currentPage = 1, int $perPage = 10): LengthAwarePaginator
    {
        return new LengthAwarePaginator($items, $total, $perPage, $currentPage);
    }

    public function test_format_menghasilkan_struktur_data_dan_meta(): void
    {
        $paginator = $this->makePaginator(['a', 'b'], total: 2);

        $result = TableResponseFormatter::format($paginator);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
    }

    public function test_meta_berisi_informasi_pagination_yang_benar(): void
    {
        $paginator = $this->makePaginator(range(1, 10), total: 35, currentPage: 2, perPage: 10);

        $result = TableResponseFormatter::format($paginator);

        $this->assertSame(2, $result['meta']['current_page']);
        $this->assertSame(4, $result['meta']['last_page']);
        $this->assertSame(35, $result['meta']['total']);
        $this->assertSame(10, $result['meta']['per_page']);
        $this->assertSame(11, $result['meta']['from']);
        $this->assertSame(20, $result['meta']['to']);
    }

    public function test_transform_diterapkan_ke_setiap_item(): void
    {
        $paginator = $this->makePaginator([1, 2, 3], total: 3);

        $result = TableResponseFormatter::format($paginator, fn ($n) => $n * 10);

        $this->assertSame([10, 20, 30], $result['data']->all());
    }

    public function test_tanpa_transform_data_dikembalikan_apa_adanya(): void
    {
        $paginator = $this->makePaginator(['x', 'y'], total: 2);

        $result = TableResponseFormatter::format($paginator);

        $this->assertSame(['x', 'y'], $result['data']->all());
    }

    public function test_dataset_kosong_menghasilkan_from_dan_to_null(): void
    {
        $paginator = $this->makePaginator([], total: 0);

        $result = TableResponseFormatter::format($paginator);

        $this->assertNull($result['meta']['from']);
        $this->assertNull($result['meta']['to']);
        $this->assertCount(0, $result['data']);
    }
}
