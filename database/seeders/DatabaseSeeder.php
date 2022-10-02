<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        switch (config('app.env')) {
            case 'production':
                $this->runProductionSeeders();

                break;
            case 'staging':
                $this->runStagingSeeders();

                break;
            case 'local':
                $this->runLocalSeeders();

                break;
            case 'testing':
                $this->runTestingSeeders();

                break;
        }
    }

    /**
     * Seed the application's database with production data.
     *
     * @return void
     */
    public function runProductionSeeders()
    {
        $this->call([
            \Database\Seeders\production\PermissionSeeder::class,
            \Database\Seeders\production\RoleSeeder::class,
            \Database\Seeders\production\UserSeeder::class
        ]);
    }

    /**
     * Seed the application's database with staging data.
     *
     * @return void
     */
    public function runStagingSeeders()
    {
        $this->call([
            \Database\Seeders\staging\PermissionSeeder::class,
            \Database\Seeders\staging\RoleSeeder::class,
            \Database\Seeders\staging\UserSeeder::class
        ]);
    }

    /**
     * Seed the application's database with local data.
     *
     * @return void
     */
    public function runLocalSeeders()
    {
        $this->call([
            \Database\Seeders\local\PermissionSeeder::class,
            \Database\Seeders\local\RoleSeeder::class,
            \Database\Seeders\local\UserSeeder::class
        ]);
    }

    /**
     * Seed the application's database with testing data.
     *
     * @return void
     */
    public function runTestingSeeders()
    {
        $this->call([
            \Database\Seeders\testing\LogSeeder::class,
            \Database\Seeders\testing\PermissionSeeder::class,
            \Database\Seeders\testing\RoleSeeder::class,
            \Database\Seeders\testing\UserSeeder::class
        ]);
    }
}
