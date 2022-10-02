<?php

namespace Database\Seeders\testing;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Activitylog\Facades\LogBatch;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            ['name' => 'Add Users'],
            ['name' => 'Edit Users'],
            ['name' => 'Delete Users'],
            ['name' => 'Add Roles'],
            ['name' => 'Edit Roles'],
            ['name' => 'Delete Roles'],
            ['name' => 'Add Permissions'],
            ['name' => 'Edit Permissions'],
            ['name' => 'Delete Permissions']
        ];

        LogBatch::startBatch();

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        LogBatch::endBatch();
    }
}
