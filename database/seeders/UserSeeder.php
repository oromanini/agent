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
            'cpf' => '111.111.111-11',
            'cnpj' => '00.000.000/0000-00',
            'phone_number' => '(44) 9 9915-5919',
            'email' => 'oscar.romanini@alluzenergia.com.br',
            'password' => Hash::make('Neia@vida.2022!'),
            'city' => 1,
            'ascendant' => 1,
            'permission' => 'admin'
        ]);
    }
}
