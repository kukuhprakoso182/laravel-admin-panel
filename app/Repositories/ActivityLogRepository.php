<?php

namespace App\Repositories;

use App\Models\ActivityLog;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use Illuminate\Support\Collection;

class ActivityLogRepository extends BaseRepository implements ActivityLogRepositoryInterface
{
    public function __construct(ActivityLog $model)
    {
        parent::__construct($model);
    }

    public function latestWithCauser(int $limit = 8): Collection
    {
        return ActivityLog::query()
            ->with('causer')
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }
}
