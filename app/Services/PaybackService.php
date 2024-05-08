<?php

namespace App\Services;

use App\Models\City;
use App\Models\Lead;
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

    public function setGenerationData(Proposal $proposal): array
    {
        $consumption = $proposal->average_consumption;
        $city = $proposal->client->addresses->first()->city;
        $incidence = $this->solarIncidenceService->getSolarIncidence($city);
        $kwp = $proposal->kwp;

        $generationData = [
            'months' => [
                'janeiro' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'jan', kwp: $kwp, incidence: $incidence),
                    'excedente' => $this->setGeneration(month: 'jan', kwp: $kwp, incidence: $incidence) - $consumption,
                ],
                'fevereiro' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'feb', kwp: $kwp, incidence: $incidence),
                    'excedente' => $this->setGeneration(month: 'feb', kwp: $kwp, incidence: $incidence) - $consumption,
                ],
                'março' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'mar', kwp: $kwp, incidence: $incidence),
                    'excedente' => $this->setGeneration(month: 'mar', kwp: $kwp, incidence: $incidence) - $consumption,
                ],
                'abril' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'apr', kwp: $kwp, incidence: $incidence),
                    'excedente' => $this->setGeneration(month: 'apr', kwp: $kwp, incidence: $incidence) - $consumption,
                ],
                'maio' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'may', kwp: $kwp, incidence: $incidence),
                    'excedente' => $this->setGeneration(month: 'may', kwp: $kwp, incidence: $incidence) - $consumption,
                ],
                'junho' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'jun', kwp: $kwp, incidence: $incidence),
                    'excedente' => $this->setGeneration(month: 'jun', kwp: $kwp, incidence: $incidence) - $consumption,
                ],
                'julho' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'jul', kwp: $kwp, incidence: $incidence),
                    'excedente' => $this->setGeneration(month: 'jul', kwp: $kwp, incidence: $incidence) - $consumption,
                ],
                'agosto' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'aug', kwp: $kwp, incidence: $incidence),
                    'excedente' => $this->setGeneration(month: 'aug', kwp: $kwp, incidence: $incidence) - $consumption,
                ],
                'setembro' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'sep', kwp: $kwp, incidence: $incidence),
                    'excedente' => $this->setGeneration(month: 'sep', kwp: $kwp, incidence: $incidence) - $consumption,
                ],
                'outubro' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'oct', kwp: $kwp, incidence: $incidence),
                    'excedente' => $this->setGeneration(month: 'oct', kwp: $kwp, incidence: $incidence) - $consumption,
                ],
                'novembro' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'nov', kwp: $kwp, incidence: $incidence),
                    'excedente' => $this->setGeneration(month: 'nov', kwp: $kwp, incidence: $incidence) - $consumption,
                ],
                'dezembro' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'dec', kwp: $kwp, incidence: $incidence),
                    'excedente' => $this->setGeneration(month: 'dec', kwp: $kwp, incidence: $incidence) - $consumption,
                ],
            ]
        ];

        $consumptionSum = 0;
        $generationSum = 0;
        $excedentSum = 0;

        foreach ($generationData['months'] as $val) {
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

    private function setGeneration(string $month, float $kwp, object $incidence): float
    {
        return ceil(
            ($kwp * 30 * ((float)$incidence->{$month} / 1000)) / (1 + (float) env('GENERATION_LOST'))
        );
    }

    public function setLeadPaybackData(Lead $lead): array
    {
        $solarIncidenceService = new SolarIncidenceService();
        $valueService = new ProposalValueHistoryService();

        $generation = (new ProposalService(
            $solarIncidenceService,
            $valueService,
        ))->calculateEstimatedGeneration(
            kwp: $lead->kit()['kwp'],
            incidence: $solarIncidenceService->getSolarIncidence($lead->city())
        )['average'];

        $kwhValue = $lead->kwh_price;
        $minTax = ($lead->average_consumption * $kwhValue) * 0.1;
        $solarSystemPrice = $lead->pricing()['final_price']['finalPrice'];
        $balance = $lead->pricing()['final_price']['finalPrice'];
        $totalEconomy = 0;
        $tusd = 0;

        $payback = [
            'years' => paybackToString(round($solarSystemPrice / ((($generation * $kwhValue) - $minTax) * 12), 1)),
        ];

        for ($count = 1; $count <= 25; $count++) {
            if ($count == 1) {

                $tusd = (formatFloat($generation) - $lead->average_consumption) * 0.09;

                $payback['data'][$count] = [
                    'economy' => formatFloat(((($generation * $kwhValue) - $minTax - $tusd) * 12)),
                    'balance' => formatFloat(((($generation * $kwhValue) - $minTax - $tusd) * 12) - $solarSystemPrice),
                    'kw_value' => formatFloat($kwhValue),
                    'generation' => formatFloat($generation),
                ];

                $balance = formatFloat($payback['data'][$count]['economy'] - $solarSystemPrice);

            } else {

                $generation -= $generation * 0.008;
                $kwhValue *= 1.03;
                $economy = ((($generation * $kwhValue) - $minTax) * 12);
                $balance += $economy;

                $payback['data'][$count] = [
                    'economy' => formatFloat($economy),
                    'balance' => formatFloat($balance),
                    'kw_value' => formatFloat($kwhValue),
                    'generation' => formatFloat($generation),
                ];

            }

            $totalEconomy += $payback['data'][$count]['economy'];
        }

        $payback['totalEconomy'] = $totalEconomy;

        return $payback;
    }


}
