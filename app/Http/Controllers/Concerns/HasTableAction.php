<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait HasTableAction
{
    abstract protected function service(): object;

    public function data(Request $request)
    {
        return response()->json($this->service()->table($request));
    }
}
