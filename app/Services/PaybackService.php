<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Proposal;

class PaybackService
{
    const YEAR_LOST_GENERATION = 0.008;
    const YEAR_KWH_INCREASE = 1.03;
    /** @var Proposal */
    private Proposal $proposal;

    public function __construct(private readonly SolarIncidenceService $solarIncidenceService, Proposal $proposal)
    {
        $this->proposal = $proposal;
    }

    public function setPaybackData(): array
    {
        $kwhPrice = $this->proposal->kw_price;
        $averageConsumption = $this->proposal->average_consumption;
        $estimatedBillValue = $averageConsumption * $kwhPrice;
        $valueHistory = $this->proposal->valueHistory;
        $estimatedGeneration = $this->proposal->estimated_generation;
        $balance = 0;
        $totalEconomy = 0;

        for ($count = 1; $count <= 25; $count++) {
            if ($count == 1) {

                $economy = $estimatedBillValue * $this->setTusd(1) * 12;

                $payback['data'][1] = [
                    'economy' => formatFloat($economy),
                    'balance' => formatFloat($economy - $valueHistory->final_price),
                    'kw_value' => formatFloat($kwhPrice),
                    'generation' => formatFloat($estimatedGeneration),
                ];

                $balance = formatFloat($payback['data'][$count]['economy'] - $valueHistory->final_price);

            } else {

                $estimatedGeneration -= $estimatedGeneration * self::YEAR_LOST_GENERATION;
                $kwhPrice *= self::YEAR_KWH_INCREASE;
                $economy = $averageConsumption * $kwhPrice * $this->setTusd($count) * 12;
                $balance += $economy;

                $payback['data'][$count] = [
                    'economy' => formatFloat($economy),
                    'balance' => formatFloat($balance),
                    'kw_value' => formatFloat($kwhPrice),
                    'generation' => formatFloat($estimatedGeneration),
                ];
            }

            $totalEconomy += $payback['data'][$count]['economy'];
        }

        $payback += ['totalEconomy' => $totalEconomy, 'years' => $this->paybackToString($payback)];
        return $payback;
    }

    public function setGenerationData(Proposal $proposal): array
    {
        $consumption = $proposal->average_consumption;
        $city = $proposal->client->addresses->first()->city;
        $incidence = $this->solarIncidenceService->getSolarIncidence($city);
        $kwp = $proposal->kwp;
        $roof_orientation = $proposal->roof_orientation;

        $generationData = [
            'months' => [
                'janeiro' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'jan', kwp: $kwp, incidence: $incidence, roof: $roof_orientation),
                    'excedente' => $this->setGeneration(month: 'jan', kwp: $kwp, incidence: $incidence, roof: $roof_orientation) - $consumption,
                ],
                'fevereiro' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'feb', kwp: $kwp, incidence: $incidence, roof: $roof_orientation),
                    'excedente' => $this->setGeneration(month: 'feb', kwp: $kwp, incidence: $incidence, roof: $roof_orientation) - $consumption,
                ],
                'março' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'mar', kwp: $kwp, incidence: $incidence, roof: $roof_orientation),
                    'excedente' => $this->setGeneration(month: 'mar', kwp: $kwp, incidence: $incidence, roof: $roof_orientation) - $consumption,
                ],
                'abril' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'apr', kwp: $kwp, incidence: $incidence, roof: $roof_orientation),
                    'excedente' => $this->setGeneration(month: 'apr', kwp: $kwp, incidence: $incidence, roof: $roof_orientation) - $consumption,
                ],
                'maio' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'may', kwp: $kwp, incidence: $incidence, roof: $roof_orientation),
                    'excedente' => $this->setGeneration(month: 'may', kwp: $kwp, incidence: $incidence, roof: $roof_orientation) - $consumption,
                ],
                'junho' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'jun', kwp: $kwp, incidence: $incidence, roof: $roof_orientation),
                    'excedente' => $this->setGeneration(month: 'jun', kwp: $kwp, incidence: $incidence, roof: $roof_orientation) - $consumption,
                ],
                'julho' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'jul', kwp: $kwp, incidence: $incidence, roof: $roof_orientation),
                    'excedente' => $this->setGeneration(month: 'jul', kwp: $kwp, incidence: $incidence, roof: $roof_orientation) - $consumption,
                ],
                'agosto' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'aug', kwp: $kwp, incidence: $incidence, roof: $roof_orientation),
                    'excedente' => $this->setGeneration(month: 'aug', kwp: $kwp, incidence: $incidence, roof: $roof_orientation) - $consumption,
                ],
                'setembro' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'sep', kwp: $kwp, incidence: $incidence, roof: $roof_orientation),
                    'excedente' => $this->setGeneration(month: 'sep', kwp: $kwp, incidence: $incidence, roof: $roof_orientation) - $consumption,
                ],
                'outubro' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'oct', kwp: $kwp, incidence: $incidence, roof: $roof_orientation),
                    'excedente' => $this->setGeneration(month: 'oct', kwp: $kwp, incidence: $incidence, roof: $roof_orientation) - $consumption,
                ],
                'novembro' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'nov', kwp: $kwp, incidence: $incidence, roof: $roof_orientation),
                    'excedente' => $this->setGeneration(month: 'nov', kwp: $kwp, incidence: $incidence, roof: $roof_orientation) - $consumption,
                ],
                'dezembro' => [
                    'consumo' => $consumption,
                    'geracao' => $this->setGeneration(month: 'dec', kwp: $kwp, incidence: $incidence, roof: $roof_orientation),
                    'excedente' => $this->setGeneration(month: 'dec', kwp: $kwp, incidence: $incidence, roof: $roof_orientation) - $consumption,
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

    private function setGeneration(string $month, float $kwp, object $incidence, string $roof): float
    {
        $totalLost = (float) env('GENERATION_LOST') + $this->setRoofPlusLost($roof);

        return ceil(
            ($kwp * 30 * ((float) $incidence->{$month} / 1000)) / (1 + $totalLost)
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

    private function paybackToString(array $payback): string
    {
        $paybackYears = 0;

        foreach ($payback['data'] as $key => $value) {
            if ($value['balance'] > 0) {
                $paybackYears = $key;
                break;
            }
        }

        return $paybackYears . ' anos';
    }

    private function setTusd(int $count): float
    {
        return 1 - match ($count) {
            1 => 0.28 * 0.45,
            2 => 0.28 * 0.60,
            3 => 0.28 * 0.75,
            4 => 0.28 * 0.90,
            default => 0.28,
        };
    }

    private function setRoofPlusLost(string $roof_orientation): float
    {
        return match ($roof_orientation) {
            '["sul"]' => 0.30,
            '["leste/oeste"]' => 0.1,
            default => 0
        };
    }
}
