<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasDestroyAction;
use App\Http\Controllers\Concerns\HasIndexView;
use App\Http\Controllers\Concerns\HasShowAction;
use App\Http\Controllers\Concerns\HasStoreAction;
use App\Http\Controllers\Concerns\HasTableAction;
use App\Http\Controllers\Concerns\HasUpdateAction;

abstract class BaseCrudController extends Controller
{
    use HasIndexView, HasTableAction, HasShowAction, HasStoreAction, HasUpdateAction, HasDestroyAction;

    abstract protected function service(): object;
}
