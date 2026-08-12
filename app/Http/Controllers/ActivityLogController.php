<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasIndexView;
use App\Http\Controllers\Concerns\HasShowAction;
use App\Http\Controllers\Concerns\HasTableAction;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    use HasIndexView, HasTableAction, HasShowAction;

    public function __construct(protected ActivityLogService $activityLogService)
    {
    }

    protected function service(): object
    {
        return $this->activityLogService;
    }

    protected function viewName(): string
    {
        return 'pages.activity-logs.index';
    }

    public function export(Request $request)
    {
        return $this->activityLogService->export($request);
    }
}