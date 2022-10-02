<?php

namespace Database\Seeders\testing;

use App\Models\Role;
use Illuminate\Database\Seeder;
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
        $roles = [
            ['name' => 'Super Admin'],
            ['name' => 'Admin'],
            ['name' => 'User']
        ];

        LogBatch::startBatch();

        foreach ($roles as $role) {
            Role::create($role);
        }

        LogBatch::endBatch();
    }
}
