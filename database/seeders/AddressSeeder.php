<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('addresses')->insert([
            'street' => 'Rua Alluz',
            'number' => '123',
            'zipcode' => '87020-030',
            'neighborhood' => 'Centro',
            'city_id' => 1,
        ]);
    }
}
