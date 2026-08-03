<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['name', 'description'];

    public function roleMenuPermissions()
    {
        return $this->hasMany(RoleMenuPermission::class);
    }
}
