<?php

namespace App\Services;

use App\Models\PreInspection;
use Illuminate\Support\Facades\DB;

class PreInspectionService
{
    public function store(): int
    {
        $preInspection = new PreInspection();
        $preInspection->roof = json_encode(array());
        $preInspection->pattern = json_encode(array());
        $preInspection->switchboard = json_encode(array());
        $preInspection->compass = json_encode(array());
        $preInspection->croqui = json_encode(array());
        $preInspection->circuit_breaker = json_encode(array());
        $preInspection->post = json_encode(array());

        DB::transaction(function () use ($preInspection) {
            $preInspection->save();
        });

        return $preInspection->id;
    }

    public function update($preInspection, $data): array
    {
        $preInspection = $this->storeFiles($data, $preInspection);

        if (isset($data['observations'])) {
            $preInspection->observations = $data['observations'];
        }

        DB::transaction(function () use ($preInspection) {
            $preInspection->update();
        });

        return ['success', 'Pré-vistoria atualizada!'];
    }

    private function storeFiles(array $data, PreInspection $preInspection): PreInspection
    {
        $store = [];

        if (isset($data['inspection']['roof'])) {
            $store['roof'] = json_decode($preInspection->roof, true);

            foreach ($data['inspection']['roof'] as $roof) {
                $store['roof'][] = $roof->store('public/inspections/' . $preInspection->id);
            }
            $preInspection->roof = json_encode($store['roof']);

        }

        if (isset($data['inspection']['pattern'])) {
            $store['pattern'] = json_decode($preInspection->pattern, true);
            $store['pattern'][] = $data['inspection']['pattern']->store('public/inspections/' . $preInspection->id);
            $preInspection->pattern = json_encode($store['pattern']);
        }

        if (isset($data['inspection']['circuit_breaker'])) {

            $store['circuit_breaker'] = json_decode($preInspection->circuit_breaker, true);
            $store['circuit_breaker'][] = $data['inspection']['circuit_breaker']->store('public/inspections/' . $preInspection->id);
            $preInspection->circuit_breaker = json_encode($store['circuit_breaker']);
        }

        if (isset($data['inspection']['switchboard'])) {
            $store['switchboard'] = json_decode($preInspection->switchboard, true);
            $store['switchboard'][] = $data['inspection']['switchboard']->store('public/inspections/' . $preInspection->id);
            $preInspection->switchboard = json_encode($store['switchboard']);
        }

        if (isset($data['inspection']['post'])) {
            $store['post'] = json_decode($preInspection->post, true);
            $store['post'][] = $data['inspection']['post']->store('public/inspections/' . $preInspection->id);
            $preInspection->post = json_encode($store['post']);
        }

        if (isset($data['inspection']['compass'])) {
            $store['compass'] = json_decode($preInspection->compass, true);
            $store['compass'][] = $data['inspection']['compass']->store('public/inspections/' . $preInspection->id);
            $preInspection->compass = json_encode($store['compass']);
        }

        if (isset($data['inspection']['croqui'])) {
            $store['croqui'] = json_decode($preInspection->croqui, true);
            $store['croqui'][] = $data['inspection']['croqui']->store('public/inspections/' . $preInspection->id);
            $preInspection->croqui = json_encode($store['croqui']);
        }

        return $preInspection;
    }
}
