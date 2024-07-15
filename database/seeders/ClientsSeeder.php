<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientsSeeder extends Seeder
{
    public function run(): void
    {
        DB::unprepared("
            INSERT INTO clients (id, uuid, name, type, document, alias, email, phone_number, agent_id, owner_document, deleted_at, created_at, updated_at, birthdate, account_owner_document) VALUES (1, '1ecd73ec-01d4-6718-9f38-0242ac160003', 'João da Silva Souza', 'person', '092.333.333-44', null, 'joao@gmail.com', '(44) 9 9995-9595', 1, null, null, '2022-05-19 06:41:45', '2022-05-19 06:41:45', null, null);
            INSERT INTO clients (id, uuid, name, type, document, alias, email, phone_number, agent_id, owner_document, deleted_at, created_at, updated_at, birthdate, account_owner_document) VALUES (2, '1ecd7b61-1bf9-68b0-b37c-0242ac160003', 'Silvia Pereira de Sá', 'person', '092.390.319-66', null, 'silvia@gmail.com', '(44) 9 9998-8982', 1, null, null, '2022-05-19 20:55:52', '2022-05-19 20:55:52', null, null);
        ");
    }
}
