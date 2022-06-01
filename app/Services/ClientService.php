<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Client;
use App\Models\ConsumerUnit;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Ramsey\Uuid\Uuid;

class ClientService implements BaseService
{

    /**
     * @throws \Exception
     */
    public function store($data): array
    {
        $client = $this->fillObject($data, null);

        try {
            DB::transaction(function () use ($client) {
                $client->uuid = Uuid::uuid6();
                $client->save();
            });

            $address = $this->storeAddress($data, $client->id);
            $consumerUnit = $this->storeconsumerUnit($data, $address->id);

        } catch (\Exception $e) {
            return ['error','Erro ao cadastrar cliente: ' . $e];
        }

        return ['success','Cliente cadastrado com sucesso!'];
    }

    public function update($id, $data): array
    {
       return [];
    }

    public function delete($id): array
    {
        return [];
    }

    /**
     * @throws \Exception
     */
    public function fillObject($data, ?object $incidence = null): object
    {
        $client = new Client();

        $client->name = $data['name'];
        $client->type = $data['type'];
        $client->document = $data['document'];
        $client->email = $data['email'];
        $client->phone_number = $data['phone_number'];

        $client->agent_id = auth()->user()->id;

        return $client;
    }

    /**
     * @throws \Exception
     */
    private function storeAddress($data, $client_id): Address
    {
        $address = new Address();

        $address->street = $data['street'];
        $address->number = $data['address_number'];
        $address->complement = isset($data['complement']) ?? null;
        $address->city_id = $data['city'];
        $address->zipcode = $data['zipcode'];
        $address->neighborhood = $data['neighborhood'];
        $address->client_id = $client_id;

        try {
            DB::transaction(function () use ($address) {
                $address->save();
            });
        } catch (\Exception $e) {
            throw new \Exception($e);
        }

        return $address;
    }

    private function storeconsumerUnit($data, $address_id): ?ConsumerUnit
    {
        $consumerUnit = new ConsumerUnit();

        if (isset($data['uc_number']) && isset($data['uc_type']) && isset($data['eletricity_bill'])) {

            $consumerUnit->number = $data['uc_number'];
            $consumerUnit->type = $data['uc_type'];
            $consumerUnit->electricity_bill = $data['eletricity_bill']->storeAs('consumer_units', 'uc_address_' . $address_id . '.pdf');
            $consumerUnit->address_id = $address_id;

            DB::transaction(function () use ($consumerUnit) {
                $consumerUnit->save();
            });

            return $consumerUnit;
        }

        return null;
    }


}
