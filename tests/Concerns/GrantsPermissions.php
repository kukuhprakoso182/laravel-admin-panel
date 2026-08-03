<?php

namespace Tests\Concerns;

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

/**
 * Helper untuk feature test yang butuh setup permission cepat,
 * tanpa mengulang boilerplate role+menu+permission+pivot di tiap test.
 */
trait GrantsPermissions
{
    /**
     * Beri sebuah role akses ke satu aksi pada satu menu.
     * Menu dan permission dibuat otomatis kalau belum ada.
     */
    protected function grant(Role $role, string $menuAlias, string $action, ?Menu $menu = null): Menu
    {
        $menu ??= Menu::firstOrCreate(
            ['link_alias' => $menuAlias],
            ['name' => $menuAlias, 'link' => '/' . str($menuAlias)->before('.'), 'order' => 1, 'is_active' => true]
        );

        $permission = Permission::firstOrCreate(
            ['name' => $action],
            ['group' => 'general', 'description' => $action]
        );

        DB::table('role_menu_permissions')->updateOrInsert(
            ['role_id' => $role->id, 'menu_id' => $menu->id, 'permission_id' => $permission->id],
            ['created_at' => now(), 'updated_at' => now()]
        );

        return $menu;
    }

    /**
     * Beri role akses penuh (view/create/edit/delete/export) ke satu menu.
     */
    protected function grantFull(Role $role, string $menuAlias): Menu
    {
        $menu = null;
        foreach (['view', 'create', 'edit', 'delete', 'export'] as $action) {
            $menu = $this->grant($role, $menuAlias, $action, $menu);
        }

        return $menu;
    }
}
