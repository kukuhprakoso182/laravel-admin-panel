<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'parent_id', 'icon_id', 'name', 'link', 'link_alias', 'order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive', 'icon');
    }

    public function icon()
    {
        return $this->belongsTo(Icon::class, 'icon_id');
    }

    public function roleMenuPermissions()
    {
        return $this->hasMany(RoleMenuPermission::class);
    }
}
