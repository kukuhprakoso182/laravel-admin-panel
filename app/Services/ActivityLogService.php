<?php

namespace App\Services;

use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use App\Support\CsvExporter;
use App\Support\TableResponseFormatter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogService
{
    public function __construct(protected ActivityLogRepositoryInterface $activityLogRepository)
    {
    }

    public function find(int|string $id)
    {
        return $this->activityLogRepository->find($id)->load('causer');
    }

    protected function baseQuery(Request $request)
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

    public function table(Request $request): array
    {
        $query = $this->baseQuery($request);

        $paginated = $this->activityLogRepository->paginateFiltered(
            request: $request,
            searchableColumns: ['description', 'event', 'ip_address'],
            sortableColumns: ['event', 'subject_type', 'created_at'],
            defaultSort: 'created_at',
            query: $query,
        );

        return TableResponseFormatter::format($paginated, fn ($log) => [
            'id' => $log->id,
            'event' => $log->event,
            'description' => $log->description,
            'subject_type' => $log->subject_type ? class_basename($log->subject_type) : null,
            'subject_id' => $log->subject_id,
            'causer' => $log->causer ? ['id' => $log->causer->id, 'name' => $log->causer->name] : null,
            'ip_address' => $log->ip_address,
            'created_at' => $log->created_at,
        ]);
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
}
