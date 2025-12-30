<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class RbacSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $permissions = [
            'project.view',
            'project.create',
            'project.update',
            'project.delete',
            'task.view',
            'task.create',
            'task.update',
            'task.delete',
        ];
        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate([
                'name' => $permission,
                'guard_name' => 'api',
            ]);
        }
        $admin = Role::query()->firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $manager = Role::query()->firstOrCreate(['name' => 'manager', 'guard_name' => 'api']);
        $member = Role::query()->firstOrCreate(['name' => 'member', 'guard_name' => 'api']);
        $admin->givePermissionTo($permissions);
        $manager->givePermissionTo([
            'project.view',
            'project.create',
            'project.update',
            'task.view',
            'task.create',
            'task.update',
        ]);
        $member->givePermissionTo([
            'task.view',
            'task.create',
            'task.update',
        ]);
    }
}
