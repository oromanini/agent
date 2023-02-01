<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Client;
use App\Models\ConsumerUnit;
use Illuminate\Http\Request;
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
            $consumerUnit = $this->storeConsumerUnit($data, $address->id);
            $address->consumer_unit_id = $consumerUnit ? $consumerUnit->id : null;

            DB::transaction(function () use ($address) {
                $address->update();
            });

        } catch (\Exception $e) {
            throw new \Exception($e);
        }

        return ['success', 'Cliente cadastrado com sucesso!'];
    }

    public function update($id, $data): array
    {
        $client = Client::find($id);
        $address = Address::query()->where('client_id', $client->id)->first();

        $client->name = $data['name'];
        $client->type = $data['type'];
        $client->document = $data['document'];
        $client->email = $data['email'];
        $client->phone_number = $data['phone_number'];
        $client->owner_document = isset($data['owner_document'])
            ? $data['owner_document']->store('public/owner_document/' . $client->id)
            : $client->owner_document;

        $address->street = $data['street'];
        $address->number = $data['address_number'];
        $address->complement = $data['complement'];
        $address->zipcode = $data['zipcode'];
        $address->neighborhood = $data['neighborhood'];
        $address->city_id = $data['city'];

        try {
            DB::transaction(function () use ($client, $address) {
                $client->update();
                $address->update();
            });

        } catch (\Exception $e) {
            throw new \Exception('Erro ao atualizar cliente: ' . $e);
        }

        return ['success', 'Cliente atualizado com sucesso!'];
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

        $client->owner_document = isset($data['owner_document'])
            ? $data['owner_document']->store('public/owner_document/' . $client->id)
            : $client->owner_document;

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

    private function storeConsumerUnit($data, $address_id): ?ConsumerUnit
    {
        $consumerUnit = new ConsumerUnit();

        if (isset($data['uc_number']) && isset($data['uc_type']) && isset($data['electricity_bill'])) {

            $consumerUnit->number = $data['uc_number'];
            $consumerUnit->type = $data['uc_type'];
            $consumerUnit->eletricity_bill = $data['electricity_bill']->store('public/consumer_units/' . $address_id);

            DB::transaction(function () use ($consumerUnit) {
                $consumerUnit->save();
            });

            return $consumerUnit;
        }

        return null;
    }


}
