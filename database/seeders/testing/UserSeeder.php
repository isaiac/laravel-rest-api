<?php

namespace Database\Seeders\testing;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Spatie\Activitylog\Facades\LogBatch;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'username' => 'superadmin',
                'roles' => [
                    ['id' => 'super-admin']
                ]
            ],
            [
                'username' => 'user-admin',
                'roles' => [
                    ['id' => 'admin']
                ],
                'permissions' => [
                    ['id' => 'add-users'],
                    ['id' => 'edit-users'],
                    ['id' => 'delete-users']
                ]
            ],
            [
                'username' => 'role-admin',
                'roles' => [
                    ['id' => 'admin']
                ],
                'permissions' => [
                    ['id' => 'add-roles'],
                    ['id' => 'edit-roles'],
                    ['id' => 'delete-roles']
                ]
            ],
            [
                'username' => 'permission-admin',
                'roles' => [
                    ['id' => 'admin']
                ],
                'permissions' => [
                    ['id' => 'add-permissions'],
                    ['id' => 'edit-permissions'],
                    ['id' => 'delete-permissions']
                ]
            ],
            [
                'username' => 'user',
                'roles' => [
                    ['id' => 'user']
                ]
            ],
            [
                'email' => 'unverified@example.com',
                'username' => 'unverified-user',
                'email_verified_at' => null,
                'roles' => [
                    ['id' => 'user']
                ]
            ],
            [
                'username' => 'loggable-user',
                'password' => 'changeme.123',
                'roles' => [
                    ['id' => 'user']
                ]
            ],
        ];

        LogBatch::startBatch();

        foreach ($users as $user) {
            $roles = isset($user['roles']) ? $user['roles'] : null;
            $permissions = isset($user['permissions']) ? $user['permissions'] : null;

            $user = User::Factory()->create(Arr::except($user, ['roles', 'permissions']));

            if (! is_null($roles)) {
                $user->syncRoles($roles);
            }

            if (! is_null($permissions)) {
                $user->syncPermissions($permissions);
            }
        }

        LogBatch::endBatch();
    }
}
