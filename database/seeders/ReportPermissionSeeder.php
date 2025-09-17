<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ReportPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $view = Permission::findOrCreate('report.summary.view', 'web');
        $export = Permission::findOrCreate('report.summary.export', 'web');

        // opsional: auto-attach ke role admin
        if ($admin = Role::where('name','admin')->first()) {
            $admin->givePermissionTo([$view, $export]);
        }
    }
}
