<?php

namespace Database\Seeders\staging;

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
        $date = '2023-01-01 00:00:00';

        $permissions = [
            [
                'name' => 'Add Users',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'name' => 'Edit Users',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'name' => 'Delete Users',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'name' => 'Add Roles',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'name' => 'Edit Roles',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'name' => 'Delete Roles',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'name' => 'Add Permissions',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'name' => 'Edit Permissions',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'name' => 'Delete Permissions',
                'created_at' => $date,
                'updated_at' => $date
            ]
        ];

        LogBatch::startBatch();

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        LogBatch::endBatch();
    }
}
