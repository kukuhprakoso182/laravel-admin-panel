<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['name', 'description'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function menuPermissions()
    {
        return $this->hasMany(RoleMenuPermission::class);
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'role_menu_permissions')
            ->withPivot('permission_id')
            ->distinct();
    }
}
