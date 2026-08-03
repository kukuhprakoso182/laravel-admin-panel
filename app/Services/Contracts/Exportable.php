<?php

namespace App\Services\Contracts;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface Exportable
{
    public function export(Request $request): StreamedResponse;
}
