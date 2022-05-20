<?php

namespace App\Services;

use App\Models\Proposal;

class PaybackService
{
    private $solarIncidenceService;

    public function __construct(SolarIncidenceService $solarIncidenceService)
    {
        $this->solarIncidenceService = $solarIncidenceService;
    }

    public function setPaybackData(Proposal $proposal): array
    {
        $generation = $proposal->estimated_generation;
        $minTax = calculateWithSolar($proposal);
        $kwValue = $proposal->kw_price;
        $solarSystemPrice = $proposal->valueHistory->final_price;
        $balance = $proposal->valueHistory->final_price;
        $totalEconomy = 0;
        $tusd = 0;

        $payback = [
            'years' => paybackToString(round($solarSystemPrice / ((($generation * $kwValue) - $minTax) * 12), 1)),
        ];

        for ($count = 1; $count <= 25; $count++) {
            if ($count == 1) {

                $tusd = (formatFloat($generation) - $proposal->average_consumption) * 0.09;

                $payback['data'][$count] = [
                    'economy' => formatFloat(((($generation * $kwValue) - $minTax - $tusd) * 12)),
                    'balance' => formatFloat(((($generation * $kwValue) - $minTax - $tusd) * 12) - $solarSystemPrice),
                    'kw_value' => formatFloat($kwValue),
                    'generation' => formatFloat($generation),
                ];

                $balance = formatFloat($payback['data'][$count]['economy'] - $solarSystemPrice);

            } else {

                $generation -= $generation * 0.008;
                $kwValue *= 1.03;
                $economy = ((($generation * $kwValue) - $minTax) * 12);
                $balance += $economy;

                $payback['data'][$count] = [
                    'economy' => formatFloat($economy),
                    'balance' => formatFloat($balance),
                    'kw_value' => formatFloat($kwValue),
                    'generation' => formatFloat($generation),
                ];

            }

            $totalEconomy += $payback['data'][$count]['economy'];
        }

        $payback['totalEconomy'] = $totalEconomy;

        return $payback;
    }

    public function setGeterationData(Proposal $proposal): array
    {
        $consumption = $proposal->average_consumption;
        $city = $proposal->client->addresses->first()->city;
        $incidence = $this->solarIncidenceService->getSolarIncidence($city);
        $kwp = $proposal->kwp;

        $generationData =  [
            'months' => [
                'janeiro' => [
                    'consumo' => $consumption,
                    'geracao' => ceil($kwp * 30 * (((float)$incidence->jan / 1000) - env('GENERATION_LOST'))),
                    'excedente' => ceil($kwp * 30 * (((float)$incidence->jan / 1000) - env('GENERATION_LOST'))) - $consumption,
                ],
                'fevereiro' => [
                    'consumo' => $consumption,
                    'geracao' => ceil($kwp * 30 * (((float)$incidence->feb / 1000) - env('GENERATION_LOST'))),
                    'excedente' => ceil($kwp * 30 * (((float)$incidence->feb / 1000) - env('GENERATION_LOST'))) - $consumption,
                ],
                'março' => [
                    'consumo' => $consumption,
                    'geracao' => ceil($kwp * 30 * (((float)$incidence->mar / 1000) - env('GENERATION_LOST'))),
                    'excedente' => ceil($kwp * 30 * (((float)$incidence->mar / 1000) - env('GENERATION_LOST'))) - $consumption,
                ],
                'abril' => [
                    'consumo' => $consumption,
                    'geracao' => ceil($kwp * 30 * (((float)$incidence->apr / 1000) - env('GENERATION_LOST'))),
                    'excedente' => ceil($kwp * 30 * (((float)$incidence->apr / 1000) - env('GENERATION_LOST'))) - $consumption,
                ],
                'maio' => [
                    'consumo' => $consumption,
                    'geracao' => ceil($kwp * 30 * (((float)$incidence->may / 1000) - env('GENERATION_LOST'))),
                    'excedente' => ceil($kwp * 30 * (((float)$incidence->may / 1000) - env('GENERATION_LOST'))) - $consumption,
                ],
                'junho' => [
                    'consumo' => $consumption,
                    'geracao' => ceil($kwp * 30 * (((float)$incidence->jun / 1000) - env('GENERATION_LOST'))),
                    'excedente' => ceil($kwp * 30 * (((float)$incidence->jun / 1000) - env('GENERATION_LOST'))) - $consumption,
                ],
                'julho' => [
                    'consumo' => $consumption,
                    'geracao' => ceil($kwp * 30 * (((float)$incidence->jul / 1000) - env('GENERATION_LOST'))),
                    'excedente' => ceil($kwp * 30 * (((float)$incidence->jul / 1000) - env('GENERATION_LOST'))) - $consumption,
                ],
                'agosto' => [
                    'consumo' => $consumption,
                    'geracao' => ceil($kwp * 30 * (((float)$incidence->aug / 1000) - env('GENERATION_LOST'))),
                    'excedente' => ceil($kwp * 30 * (((float)$incidence->aug / 1000) - env('GENERATION_LOST'))) - $consumption,
                ],
                'setembro' => [
                    'consumo' => $consumption,
                    'geracao' => ceil($kwp * 30 * (((float)$incidence->sep / 1000) - env('GENERATION_LOST'))),
                    'excedente' => ceil($kwp * 30 * (((float)$incidence->sep / 1000) - env('GENERATION_LOST'))) - $consumption,
                ],
                'outubro' => [
                    'consumo' => $consumption,
                    'geracao' => ceil($kwp * 30 * (((float)$incidence->oct / 1000) - env('GENERATION_LOST'))),
                    'excedente' => ceil($kwp * 30 * (((float)$incidence->oct / 1000) - env('GENERATION_LOST'))) - $consumption,
                ],
                'novembro' => [
                    'consumo' => $consumption,
                    'geracao' => ceil($kwp * 30 * (((float)$incidence->nov / 1000) - env('GENERATION_LOST'))),
                    'excedente' => ceil($kwp * 30 * (((float)$incidence->nov / 1000) - env('GENERATION_LOST'))) - $consumption,
                ],
                'dezembro' => [
                    'consumo' => $consumption,
                    'geracao' => ceil($kwp * 30 * (((float)$incidence->dec / 1000) - env('GENERATION_LOST'))),
                    'excedente' => ceil($kwp * 30 * (((float)$incidence->dec / 1000) - env('GENERATION_LOST'))) - $consumption,
                ],
            ]
        ];

        $consumptionSum = 0;
        $generationSum = 0;
        $excedentSum = 0;

        foreach ($generationData['months'] as $key => $val) {
            $consumptionSum += $val['consumo'];
            $generationSum += $val['geracao'];
            $excedentSum += $val['excedente'];
        }

        $generationData['sum'] = [
            'consumptionSum' => $consumptionSum,
            'generationSum' => $generationSum,
            'excedentSum' => $excedentSum,
        ];

        return $generationData;
    }

}
