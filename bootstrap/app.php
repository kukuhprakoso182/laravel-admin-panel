<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        // URUTAN PENTING: callback dicek dari atas ke bawah, yang pertama
        // mengembalikan response menang. QueryException turunan PDOException
        // -> RuntimeException, jadi HARUS di atas handler RuntimeException.
        $exceptions->render(function (QueryException $e, $request) {
            // Koneksi DB down — jangan expose SQL mentah
            if (str_contains($e->getMessage(), 'Connection refused') || $e->getCode() === '2002') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Layanan sedang tidak dapat diakses. Silakan coba beberapa saat lagi.',
                    ], 503);
                }

                return response()->view('errors.503', [], 503);
            }
            // QueryException lain lanjut ke handler di bawah
        });

        // Data tidak ditemukan (find() gagal) — berlaku untuk SEMUA modul otomatis
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Data tidak ditemukan.'], 404);
            }
        });

        // Laravel mengubah ModelNotFoundException menjadi NotFoundHttpException
        // SEBELUM callback ini dijalankan, sehingga handler di atas mungkin tidak
        // pernah terpanggil dan pesan "No query results for model [App\Models\...]"
        // (berisi nama class) ikut keluar. Handler ini menutup celah itu.
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($e->getPrevious() instanceof ModelNotFoundException && $request->expectsJson()) {
                return response()->json(['message' => 'Data tidak ditemukan.'], 404);
            }
        });

        // Service layer melempar RuntimeException untuk error bisnis.
        // HttpException (abort(403/404/401), dst) juga turunan RuntimeException
        // di Symfony, jadi dikecualikan supaya status aslinya tidak tertimpa.
        $exceptions->render(function (RuntimeException $e, $request) {
            if ($e instanceof HttpExceptionInterface) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Internal Service Error'], 422);
            }
        });

        // PALING AKHIR: penangkap semua error tak terduga (class tidak ketemu,
        // BindingResolutionException, TypeError, Error PHP, dst) supaya detailnya
        // tidak pernah sampai ke client, berapa pun nilai APP_DEBUG.
        $exceptions->render(function (Throwable $e, $request) {
            // Exception berikut punya response sendiri yang memang untuk user
            if (
                $e instanceof HttpExceptionInterface      // abort(), 404 route, 419, 429, dst
                || $e instanceof ValidationException       // 422 dari validator
                || $e instanceof HttpResponseException     // dipakai BaseFormRequest
                || $e instanceof AuthenticationException   // 401
                || $e instanceof TokenMismatchException    // 419
            ) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Internal Service Error'], 500);
            }
            // Request halaman biasa: Laravel menampilkan errors/500.blade.php
            // (atau halaman "Server Error" bawaan kalau APP_DEBUG=false).
        });
    })->create();
