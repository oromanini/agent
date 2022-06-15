<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\ConsumerUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function store(Request $request, $clientId)
    {
        $data = $request->all();
        $address = Address::query()->where('client_id', $clientId)->first();

        try {

            if (is_null($address->consumer_unit_id)) {

                $uc = new ConsumerUnit();
                $uc->number = (int)$data['uc_number'];
                $uc->type = $data['uc_type'];
                $uc->eletricity_bill = $data['electricity_bill']->store('public/consumer_units/'. $address->id);

                DB::transaction(function () use ($uc, $address) {
                    $uc->save();
                    $address->consumer_unit_id = $uc->id;
                    $address->update();
                });
            } else {

                $address = new Address();
                $address->street = $data['street'];
                $address->number = $data['address_number'];
                $address->complement = isset($data['complement']) ?? null;
                $address->city_id = $data['city'];
                $address->zipcode = $data['zipcode'];
                $address->neighborhood = $data['neighborhood'];
                $address->client_id = $clientId;

                $consumerUnit = new ConsumerUnit();
                $consumerUnit->number = $data['uc_number'];
                $consumerUnit->type = $data['uc_type'];

                DB::transaction(function () use ($address) {
                    $address->save();
                });

                $consumerUnit->eletricity_bill = $data['electricity_bill']->store('public/consumer_units/'. $address->id);

                DB::transaction(function () use ($consumerUnit) {
                    $consumerUnit->save();
                });

                $address->consumer_unit_id = $consumerUnit->id;

                DB::transaction(function () use ($address) {
                    $address->update();
                });

            }

        } catch (\Exception $e) {
            throw new \Exception('Erro ao atualizar UC: ' . $e);
        }

        session()->flash('message', ['success', 'UC atualizada!']);
        return redirect()->route('client.index');
    }
}
