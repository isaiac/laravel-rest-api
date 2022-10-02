<?php

namespace Database\Seeders\testing;

use Database\Factories\LogFactory;
use Illuminate\Database\Seeder;

class LogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (new LogFactory)->count(2)->create();
    }
}
