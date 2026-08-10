<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Data tidak ditemukan (find() gagal) — berlaku untuk SEMUA modul otomatis
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Data tidak ditemukan.'], 404);
            }
        });

        $exceptions->render(function (QueryException $e, $request) {
            // Koneksi DB down (connection refused, unknown host, dsb) — jangan expose SQL mentah
            if (str_contains($e->getMessage(), 'Connection refused') || $e->getCode() === '2002') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Layanan sedang tidak dapat diakses. Silakan coba beberapa saat lagi.',
                    ], 503);
                }

                return response()->view('errors.503', [], 503);
            }
        });
    })->create();
