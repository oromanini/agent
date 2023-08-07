<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        DB::unprepared("
            INSERT INTO addresses (id, street, number, complement, zipcode, neighborhood, city_id, client_id, consumer_unit_id, deleted_at, created_at, updated_at) VALUES (1, 'Rua José Clemente', '50', '1', '87020-070', 'Zona 07', 4739, 1, null, null, '2022-05-19 06:41:45', '2022-05-19 06:41:45');
            INSERT INTO addresses (id, street, number, complement, zipcode, neighborhood, city_id, client_id, consumer_unit_id, deleted_at, created_at, updated_at) VALUES (2, 'Rua Arthur Thomas', '650', '1', '87013-250', 'Zona 01', 4752, 2, null, null, '2022-05-19 20:55:52', '2022-05-19 20:55:52');
        ");
    }
}
