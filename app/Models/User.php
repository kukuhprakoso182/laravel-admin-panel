<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, LogsActivity;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relasi many-to-many ke Role lewat tabel user_roles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    /**
     * Cek apakah user memiliki role tertentu (berdasarkan nama)
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Cek permission, digabung (union) dari SEMUA role yang dimiliki user.
     * Tidak butuh "active role" karena tidak ada fitur switch role.
     */
    public function hasPermission(string $permissionName, ?int $menuId = null): bool
    {
        $roleIds = $this->roles()->pluck('roles.id');

        $query = RoleMenuPermission::whereIn('role_id', $roleIds, 'and', false)
            ->whereHas('permission', fn ($q) => $q->where('name', $permissionName));

        if ($menuId) {
            $query->where('menu_id', $menuId);
        }

        return $query->exists();
    }

    /**
     * Ambil semua menu yang bisa diakses user ini,
     * hasil gabungan dari semua role yang dimiliki (tanpa duplikat).
     */
    public function accessibleMenus()
    {
        $roleIds = $this->roles()->pluck('roles.id');

        return Menu::whereHas('roleMenuPermissions', function ($q) use ($roleIds) {
            $q->whereIn('role_id', $roleIds);
        })
        ->where('is_active', true)
        ->orderBy('order')
        ->get();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
