<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Arr;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Daftar semua permissions (union dari semua role)
        $allPermissions = [
            'dashboard.view',
            'masterfile.view',
            'masterfile.show',
            'masterfile.create',
            'masterfile.monthly',
            'coordinator.view',
            'kltg.edit',
            'outdoor.edit',
            'media.edit',
            'export.run',
            'calendar.manage', // opsional – tetap dibuat supaya bisa dipakai kapanpun
        ];

        foreach ($allPermissions as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // 2) Mapping role → permissions
        $map = [
            'admin' => ['*'], // semua permission
            'support' => [
                'dashboard.view',
                'masterfile.view',
                'masterfile.show',
                'masterfile.create',
                'masterfile.monthly',
                'coordinator.view',
                'kltg.edit',
                'outdoor.edit',
                'media.edit',
                'export.run',
                // hapus yang ini bila nggak perlu:
                'calendar.manage',
            ],
            'user' => [
                'dashboard.view',
                'masterfile.view',
            ],
        ];

        foreach ($map as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            if (in_array('*', $perms, true)) {
                $role->syncPermissions($allPermissions);
            } else {
                $role->syncPermissions($perms);
            }
        }
    }
}
