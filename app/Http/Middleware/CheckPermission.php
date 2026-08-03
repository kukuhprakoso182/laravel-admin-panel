<?php

namespace App\Http\Middleware;

use App\Services\MenuAliasResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function __construct(protected MenuAliasResolver $menuAliasResolver)
    {
    }

    public function handle(Request $request, Closure $next, string $permission, ?string $menuAlias = null): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        if ($user->status !== 'active') {
            abort(403, 'Akun Anda tidak aktif.');
        }

        $menuId = null;

        if ($menuAlias) {
            $menuId = $this->menuAliasResolver->resolve($menuAlias);

            // Alias disebut di route tapi tidak ketemu menu-nya (typo, atau
            // menu terhapus) -> DENY, jangan diam-diam lolos jadi permission
            // generik tanpa filter menu. Fail-safe, bukan fail-open.
            if (!$menuId) {
                abort(403, 'Konfigurasi permission untuk fitur ini tidak valid.');
            }
        }

        if (!$user->hasPermission($permission, $menuId)) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses fitur ini.');
        }

        return $next($request);
    }
}
