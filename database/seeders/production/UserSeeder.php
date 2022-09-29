<?php

namespace Database\Seeders\production;

use App\Models\User;
use Illuminate\Database\Seeder;
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
        $date = '2023-01-01 00:00:00+00';

        LogBatch::startBatch();

        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@email.com',
            'username' => 'superadmin',
            'password' => 'superadmin',
            'email_verified_at' => $date,
            'created_at' => $date,
            'updated_at' => $date
        ]);

        $user->syncRoles([
            [
                'id' => 'super-admin',
                'pivot' => [
                    'created_at' => $date,
                    'updated_at' => $date
                ]
            ]
        ]);

        LogBatch::endBatch();
    }
}
