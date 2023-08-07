<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            StatesSeeder::class,
            CitiesSeeder::class,
            ClientsSeeder::class,
            AddressSeeder::class,
        ]);
    }
}
