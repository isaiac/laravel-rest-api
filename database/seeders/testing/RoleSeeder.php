<?php

namespace Database\Seeders\testing;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Spatie\Activitylog\Facades\LogBatch;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = '2023-01-01 00:00:00+00';

        $roles = [
            [
                'name' => 'Super Admin',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'name' => 'Admin',
                'created_at' => $date,
                'updated_at' => $date,
                'permissions' => [
                    [
                        'id' => 'add-users',
                        'pivot' => [
                            'created_at' => $date,
                            'updated_at' => $date
                        ]
                    ],
                    [
                        'id' => 'edit-users',
                        'pivot' => [
                            'created_at' => $date,
                            'updated_at' => $date
                        ]
                    ],
                    [
                        'id' => 'delete-users',
                        'pivot' => [
                            'created_at' => $date,
                            'updated_at' => $date
                        ]
                    ]
                ]
            ],
            [
                'name' => 'User',
                'created_at' => $date,
                'updated_at' => $date
            ]
        ];

        LogBatch::startBatch();

        foreach ($roles as $role) {
            $permissions = isset($role['permissions']) ? $role['permissions'] : null;

            $role = Role::create(Arr::except($role, ['permissions']));

            if (! is_null($permissions)) {
                $role->syncPermissions($permissions);
            }
        }

        LogBatch::endBatch();
    }
}
