<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // SEMUA permissions - lengkap!
        $allPermissions = [
            // Dashboard
            'dashboard.view',

            // Master Files (support both naming conventions)
            'masterfile.view',
            'masterfile.show',
            'masterfile.create',
            'masterfile.edit',
            'masterfile.delete',
            'masterfile.monthly',

            // KLTG
            'kltg.view',
            'kltg.edit',
            'kltg.create',
            'kltg.delete',
            'kltg.monthly',

            // Social Media
            'media.view',
            'media.edit',
            'media.create',
            'media.delete',
            'media.monthly',

            // Outdoor
            'outdoor.view',
            'outdoor.edit',
            'outdoor.create',
            'outdoor.delete',
            'outdoor.monthly',
            'outdoor.whiteboard',
            'outdoor.inventory',
            'outdoor.availability',

            // Coordinator
            'coordinator.view',
            'coordinator.edit',
            'coordinator.list',

            // Export & Calendar
            'export.run',
            'calendar.view',
            'calendar.manage',

            // Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Management
            'management.view',
        ];

        foreach ($allPermissions as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // Mapping role → permissions
        $map = [
            'admin' => ['*'], // semua permission
            'support' => [
                'dashboard.view',
                'masterfile.view',
                'masterfile.show',
                'masterfile.create',
                'masterfile.edit',
                'masterfile.monthly',
                'coordinator.view',
                'coordinator.list',
                'kltg.view',
                'kltg.edit',
                'outdoor.view',
                'outdoor.edit',
                'media.view',
                'media.edit',
                'export.run',
                'calendar.manage',
            ],
            'user' => [
                'dashboard.view',
                'masterfile.view',
                'masterfile.show',
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
