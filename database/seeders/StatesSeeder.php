<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatesSeeder extends Seeder
{
    public function run(): void
    {
        DB::unprepared("
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (1, 'ACRE', 'NORTE', '2020-09-28 12:23:08', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (2, 'ALAGOAS', 'NORTE', '2020-09-28 12:23:10', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (3, 'AMAZONAS', 'NORTE', '2020-09-28 12:23:10', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (4, 'AMAPA', 'NORTE', '2020-09-28 12:23:10', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (5, 'BAHIA', 'NORDESTE', '2020-09-28 12:23:10', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (6, 'CEARA', 'NORDESTE', '2020-09-28 12:23:10', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (7, 'DISTRITO FEDERAL', 'CENTRO-OESTE', '2020-09-28 12:23:10', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (8, 'ESPIRITO SANTO', 'SUDESTE', '2020-09-28 12:56:05', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (9, 'GOIAS', 'CENTRO-OESTE', '2020-09-28 12:59:35', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (10, 'MARANHÃO', 'NORDESTE', '2020-09-28 13:02:05', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (11, 'MINAS GERAIS', 'SUDESTE', '2020-09-28 13:05:03', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (12, 'MATO GROSSO DO SUL', 'CENTRO-OESTE', '2020-09-28 13:11:00', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (13, 'MATO GROSSO', 'CENTRO-OESTE', '2020-09-28 13:14:08', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (14, 'PARÁ', 'NORDESTE', '2020-09-28 13:17:13', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (15, 'PARAÍBA', 'NORDESTE', '2020-09-28 13:19:38', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (16, 'PERNAMBUCO', 'NORDESTE', '2020-09-28 13:21:38', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (17, 'PIAUÍ', 'NORDESTE', '2020-09-28 13:23:39', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (18, 'PARANÁ', 'SUL', '2020-09-28 13:25:20', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (19, 'RIO DE JANEIRO', 'SUDESTE', '2020-09-28 13:28:22', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (20, 'RIO GRANDE DO NORTE', 'NORDESTE', '2020-09-28 13:29:46', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (21, 'RONDÔNIA', 'NORTE', '2020-09-28 13:50:41', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (22, 'RORAIMA', 'NORTE', '2020-09-28 13:54:02', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (23, 'RIO GRANDE DO SUL', 'SUL', '2020-09-28 13:55:22', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (24, 'SANTA CATARINA', 'SUL', '2020-09-28 13:57:11', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (25, 'SERGIPE', 'NORDESTE', '2020-09-28 13:58:43', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (26, 'SÃO PAULO', 'SUDESTE', '2020-09-28 14:00:50', null);
                    INSERT INTO states (id, name, region, created_at, updated_at) VALUES (27, 'TOCANTINS', 'NORTE', '2020-09-28 14:07:18', null);
                    "
        );
    }
}
