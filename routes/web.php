<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IconController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route root — redirect otomatis sesuai status login, tidak perlu middleware
// karena logikanya sendiri yang menentukan tujuan (bukan memblokir akses)
Route::get('/', function () {
    return redirect()->route(Auth::check() ? 'dashboard' : 'login');
});

// Hanya bisa diakses kalau BELUM login (middleware bawaan Laravel juga)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'reset'])->name('password.update');
});

// Hanya bisa diakses kalau SUDAH login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::middleware('permission:view,users.index')->get('/users/data', [UserController::class, 'data'])->name('users.data');
    Route::middleware('permission:view,users.index')->get('/users/export', [UserController::class, 'export'])->name('users.export');
    Route::middleware('permission:view,users.index')->get('/users/{id}', [UserController::class, 'show']);
    Route::middleware('permission:create,users.index')->post('/users', [UserController::class, 'store'])->name('users.store');
    Route::middleware('permission:edit,users.index')->put('/users/{id}', [UserController::class, 'update']);
    Route::middleware('permission:delete,users.index')->delete('/users/{id}', [UserController::class, 'destroy']);

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::middleware('permission:view,roles.index')->get('/roles/data', [RoleController::class, 'data'])->name('roles.data');
    Route::middleware('permission:view,roles.index')->get('/roles/{id}', [RoleController::class, 'show']);
    Route::middleware('permission:create,roles.index')->post('/roles', [RoleController::class, 'store']);
    Route::middleware('permission:edit,roles.index')->put('/roles/{id}', [RoleController::class, 'update']);
    Route::middleware('permission:edit,roles.index')->get('/roles/{id}/permissions', [RoleController::class, 'permissionMatrix']);
    Route::middleware('permission:edit,roles.index')->post('/roles/{id}/permissions', [RoleController::class, 'syncPermissions']);
    Route::middleware('permission:delete,roles.index')->delete('/roles/{id}', [RoleController::class, 'destroy']);

    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::middleware('permission:view,permissions.index')->get('/permissions/data', [PermissionController::class, 'data'])->name('permissions.data');
    Route::middleware('permission:view,permissions.index')->get('/permissions/{id}', [PermissionController::class, 'show']);
    Route::middleware('permission:create,permissions.index')->post('/permissions', [PermissionController::class, 'store']);
    Route::middleware('permission:edit,permissions.index')->put('/permissions/{id}', [PermissionController::class, 'update']);
    Route::middleware('permission:delete,permissions.index')->delete('/permissions/{id}', [PermissionController::class, 'destroy']);

    Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
    Route::middleware('permission:view,menus.index')->get('/menus/data', [MenuController::class, 'data'])->name('menus.data');
    Route::middleware('permission:view,menus.index')->get('/menus/tree', [MenuController::class, 'tree']);
    Route::middleware('permission:view,menus.index')->get('/menus/{id}', [MenuController::class, 'show']);
    Route::middleware('permission:create,menus.index')->post('/menus', [MenuController::class, 'store']);
    Route::middleware('permission:edit,menus.index')->put('/menus/{id}', [MenuController::class, 'update']);
    Route::middleware('permission:delete,menus.index')->delete('/menus/{id}', [MenuController::class, 'destroy']);

    Route::get('/icons', [IconController::class, 'index'])->name('icons.index');
    Route::middleware('permission:view,icons.index')->get('/icons/data', [IconController::class, 'data'])->name('icons.data');
    Route::middleware('permission:view,icons.index')->get('/icons/{id}', [IconController::class, 'show']);
    Route::middleware('permission:create,icons.index')->post('/icons', [IconController::class, 'store']);
    Route::middleware('permission:edit,icons.index')->put('/icons/{id}', [IconController::class, 'update']);
    Route::middleware('permission:delete,icons.index')->delete('/icons/{id}', [IconController::class, 'destroy']);

    Route::middleware('permission:view,activity-logs.index')->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/data', [ActivityLogController::class, 'data'])->name('activity-logs.data');
        Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
        Route::get('/activity-logs/{id}', [ActivityLogController::class, 'show']);
    });
});
