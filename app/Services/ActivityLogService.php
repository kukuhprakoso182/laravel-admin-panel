<?php

namespace App\Services;

use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use App\Services\Concerns\HasTable;
use App\Support\CsvExporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogService
{
    use HasTable;

    public function __construct(protected ActivityLogRepositoryInterface $activityLogRepository)
    {
    }

    protected function repository(): object
    {
        return $this->activityLogRepository;
    }

    public function find(int|string $id)
    {
        return $this->activityLogRepository->find($id)->load('causer');
    }

    public function export(Request $request): StreamedResponse
    {
        $query = $this->baseQuery($request);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('event', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        return CsvExporter::stream(
            rows: $logs,
            headers: ['Waktu', 'User', 'Event', 'Deskripsi', 'Subject', 'IP Address'],
            mapRow: fn ($log) => [
                optional($log->created_at)->format('Y-m-d H:i:s'),
                optional($log->causer)->name ?? 'System',
                $log->event,
                $log->description,
                $log->subject_type ? class_basename($log->subject_type) . ' #' . $log->subject_id : '-',
                $log->ip_address,
            ],
            filenamePrefix: 'activity-logs',
        );
    }

    protected function baseQuery(Request $request): Builder
    {
        $query = $this->activityLogRepository->query()->with('causer');

        if ($request->filled('event')) {
            $query->where('event', $request->get('event'));
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->get('subject_type'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        return $query;
    }

    protected function searchableColumns(): array
    {
        return ['description', 'event', 'ip_address'];
    }

    protected function sortableColumns(): array
    {
        return ['event', 'subject_type', 'created_at'];
    }

    protected function defaultSort(): string
    {
        return 'created_at';
    }

    protected function formatRow(mixed $item): array
    {
        return [
            'id' => $item->id,
            'event' => $item->event,
            'description' => $item->description,
            'subject_type' => $item->subject_type ? class_basename($item->subject_type) : null,
            'subject_id' => $item->subject_id,
            'causer' => $item->causer ? ['id' => $item->causer->id, 'name' => $item->causer->name] : null,
            'ip_address' => $item->ip_address,
            'created_at' => $item->created_at,
        ];
    }
}
