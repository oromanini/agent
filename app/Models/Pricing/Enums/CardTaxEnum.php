<?php

namespace App\Models\Pricing\Enums;

enum CardTaxEnum: string
{
    case ONE_INSTALLMENT = '2.98';
    case TWO_INSTALLMENTS = '3.70';
    case THREE_INSTALLMENTS = '4.25';
    case FOUR_INSTALLMENTS = '4.79';
    case FIVE_INSTALLMENTS = '5.34';
    case SIX_INSTALLMENTS = '5.87';
    case SEVEN_INSTALLMENTS = '6.27';
    case EIGHT_INSTALLMENTS = '6.80';
    case NINE_INSTALLMENTS = '7.32';
    case TEN_INSTALLMENTS = '7.84';
    case ELEVEN_INSTALLMENTS = '8.36';
    case TWELVE_INSTALLMENTS = '8.88';
    case THIRTEEN_INSTALLMENTS = '9.38';
    case FOURTEEN_INSTALLMENTS = '9.89';
    case FIFTEEN_INSTALLMENTS = '10.39';
    case SIXTEEN_INSTALLMENTS = '10.88';
    case SEVENTEEN_INSTALLMENTS = '11.38';
    case EIGHTEEN_INSTALLMENTS = '11.87';

    public static function calculateInstallments(float $baseValue): array
    {
        $installments = [];

        foreach (self::cases() as $case) {
            $taxRate = floatval($case->value) / 100;

            $numInstallments = match ($case->name) {
                'ONE_INSTALLMENT' => 1,
                'TWO_INSTALLMENTS' => 2,
                'THREE_INSTALLMENTS' => 3,
                'FOUR_INSTALLMENTS' => 4,
                'FIVE_INSTALLMENTS' => 5,
                'SIX_INSTALLMENTS' => 6,
                'SEVEN_INSTALLMENTS' => 7,
                'EIGHT_INSTALLMENTS' => 8,
                'NINE_INSTALLMENTS' => 9,
                'TEN_INSTALLMENTS' => 10,
                'ELEVEN_INSTALLMENTS' => 11,
                'TWELVE_INSTALLMENTS' => 12,
                'THIRTEEN_INSTALLMENTS' => 13,
                'FOURTEEN_INSTALLMENTS' => 14,
                'FIFTEEN_INSTALLMENTS' => 15,
                'SIXTEEN_INSTALLMENTS' => 16,
                'SEVENTEEN_INSTALLMENTS' => 17,
                'EIGHTEEN_INSTALLMENTS' => 18,
            };

            $totalValueWithTax = $baseValue / (1 - $taxRate);

            $installments[$numInstallments] =
                round($totalValueWithTax / $numInstallments, 2);
        }

        return $installments;
    }
}
