<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('users')->insert([
            'name' => 'Oscar Romanini',
            'document' => '111.111.111-11',
            'phone' => '(44) 9 9915-5919',
            'email' => 'oscar.romanini@alluzenergia.com.br',
            'password' => Hash::make('Neia@vida.2022!'),
            'address_id' => 1
        ]);

        DB::table('users')->insert([
            'name' => 'Natanael Cavalli',
            'document' => '111.111.111-12',
            'phone' => '(44) 9 9915-5919',
            'email' => 'natanael.cavalli@alluzenergia.com.br',
            'password' => Hash::make('Neno.2022!'),
            'address_id' => 1
        ]);
    }
}
