<?php

namespace App\Http\Controllers\Concerns;

trait HasIndexView
{
    abstract protected function viewName(): string;

    public function index()
    {
        return view($this->viewName());
    }
}
