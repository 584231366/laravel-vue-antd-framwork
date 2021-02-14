<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 重置角色和权限的缓存
        app()['cache']->forget('spatie.permission.cache');

        // 创建权限
        Permission::create(['name' => 'users-select',"guard_name"=>'api']);
        Permission::create(['name' => 'users-edit',"guard_name"=>'api']);
        Permission::create(['name' => 'roles-select',"guard_name"=>'api']);
        Permission::create(['name' => 'roles-edit',"guard_name"=>'api']);
        Permission::create(['name' => 'permissions-select',"guard_name"=>'api']);
        Permission::create(['name' => 'permissions-edit',"guard_name"=>'api']);

        // 创建角色并赋予已创建的权限
        $role = Role::create(['name' => 'super-admin',"guard_name"=>'api']);
        $role->givePermissionTo('users-select');
        $role->givePermissionTo('users-edit');
        $role->givePermissionTo('roles-select');
        $role->givePermissionTo('roles-edit');
        $role->givePermissionTo('permissions-select');
        $role->givePermissionTo('permissions-edit');

        $user = \App\Models\User::first();
        $user->assignRole("super-admin");
    }
}
