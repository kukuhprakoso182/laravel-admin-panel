<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct(protected ActivityLogService $activityLogService)
    {
    }

    public function index()
    {
        return view('pages.activity-logs.index');
    }

    public function data(Request $request)
    {
        return response()->json($this->activityLogService->table($request));
    }

    public function export(Request $request)
    {
        return $this->activityLogService->export($request);
    }

    public function show(int|string $id)
    {
        return response()->json($this->activityLogService->find($id));
    }
}
