<?php

namespace App\Services;

use App\Models\Installation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstallationService extends AfterSalesProcessService
{
    public const CHECKLIST_ITEM_APPOINTMENT_MADE = 'Agendamento realizado';
    public const CHECKLIST_ITEM_CA_PURCHASED = 'C.A Comprado';
    public const CHECKLIST_ITEM_INSTALLATION_DONE = 'Instalação Concluída';

    public static function getChecklist(): string
    {
        return json_encode([
            self::CHECKLIST_ITEM_APPOINTMENT_MADE => false,
            self::CHECKLIST_ITEM_CA_PURCHASED => false,
            self::CHECKLIST_ITEM_INSTALLATION_DONE => false,
        ]);
    }

    public function addPlusCost(Installation $installation, array $data): void
    {
        DB::transaction(function () use ($installation, $data) {

            $new_cost = [
                'description' => $data['plus_cost_description'],
                'value' => $data['plus_cost_value'],
                'proof_of_payment' => $data['plus_cost_proof_of_payment']->store("public/installation/plus_cost/proof_of_payment/$installation->id"),
                'invoice' => $data['plus_cost_invoice']->store("public/installation/plus_cost/invoice/$installation->id")
            ];

            $other_expenses = is_null($installation->other_expenses)
                ? []
                : json_decode($installation->other_expenses, true);

            $other_expenses[] = $new_cost;

            $installation->other_expenses = json_encode($other_expenses);

            $installation->update();
        });
    }

    public function setCostSums($installation): array
    {
        $costsSum['plusCosts'] = 0;
        $otherExpenses = !is_null($installation->other_expenses)
            ? jsonToArray($installation->other_expenses)
            : [];

        foreach ($otherExpenses as $cost) {
            $costsSum['plusCosts'] += stringMoneyToFloat($cost['value']);
        }

        $costsSum['totalCost'] = $installation->ca_cost + $installation->installation_cost + $costsSum['plusCosts'];

        $finalPrice = $installation->proposal->valueHistory->final_price;

        //TODO: get value history costs
        $costsSum['previousMargin'] = ($finalPrice * 0.045) + ($installation->proposal->number_of_panels * 150);

        return $costsSum;
    }

    public function updatePictures(Installation $installation, Request $request)
    {
        $pictures = jsonToArray($installation->installation_images);

        foreach ($request->allFiles() as $name => $file) {
            $path = "/public/installation/pictures/$name/$installation->id";
            $name == 'panels'
                ? $pictures[$name][] = $file->store($path)
                : $pictures[$name] = $file->store($path);
        }


        DB::transaction(function () use ($installation, $pictures) {
            $installation->installation_images = json_encode($pictures);
            $installation->update();
        });
    }
}
