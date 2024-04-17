<?php


namespace App\Services;

use App\Enums\PaymentTypeEnum;
use App\Enums\RoofStructure;
use App\Models\PromotionalKit;

class PricingService
{
    const SOLO_PLUS = 1.35;
    const LIQUID_PROFIT_PERCENTAGE = 0.13;
    const PLUS_TO_ADJUST_MARGIN = 250;

    private float $netProfit;

    public function calculateFinalPrice(
        float  $cost,
        float  $kwp,
        int    $panelCount,
        string $panelBrand,
        float  $panelPower,
        string $inverterBrand,
        int    $roofStructure,
        float  $finalValue,
        int    $paymentType
    ): array
    {
        $finalValue = $this->adjustMargin($cost, $kwp, $panelCount, $finalValue, $paymentType);

        if ($roofStructure == RoofStructure::SOLO) {
            return $this->priceWithSolo($finalValue);
        }
        return $this->findOrFailPromotionalKits($kwp, $panelBrand, $panelPower, $inverterBrand, $finalValue);
    }

    private function adjustMargin(
        float $cost,
        float $kwp,
        int   $panelCount,
        float $finalValue,
        int   $paymentType
    ): float
    {
        $this->netProfit =
            $this->calculateNetProfit(
                cost: $cost,
                kwp: $kwp,
                panelCount: $panelCount,
                finalValue: $finalValue,
                paymentType: $paymentType
            )['netProfitPercent'];

        if ($this->netProfit < self::LIQUID_PROFIT_PERCENTAGE) {
            $finalValue += self::PLUS_TO_ADJUST_MARGIN;
            $finalValue = $this->adjustMargin($cost, $kwp, $panelCount, $finalValue, $paymentType);
        }

        return $finalValue;
    }

    private function calculateNetProfit(
        float $cost,
        float $kwp,
        int   $panelCount,
        float $finalValue,
        int   $paymentType
    ): array {

        $paymentTypeTotalCost = $this->getPaymentTypeTotalCost($paymentType);

        $totalCost = (new $paymentTypeTotalCost(
            cost: $cost,
            panelCount: $panelCount,
            kwp: $kwp,
            finalValue: $finalValue,
            paymentType: $paymentType
        ))->cost();

        $netProfitValue = $finalValue - $totalCost;
        $netProfitPercent = ($finalValue / $totalCost) - 1;

        return $this->format(
            $netProfitValue,
            $netProfitPercent,
        );
    }

    private function priceWithSolo(float $finalValue): array
    {
        return [
            'finalPrice' => $finalValue * self::SOLO_PLUS,
            'isPromotional' => false
        ];
    }

    private function findOrFailPromotionalKits(
        float  $kwp,
        string $panelBrand,
        string $panelPower,
        string $inverterBrand,
        float  $finalPrice
    ): array
    {
        $promotionalKits = PromotionalKit::where('is_active', true)->get();
        $isPromotional = false;

        $promotionalKits->each(function ($promotion) use (
            $kwp,
            $panelPower,
            $panelBrand,
            $inverterBrand,
            &$finalPrice,
            &$isPromotional
        ) {
            $kitMatchPromotion = $this->kitMatchPromotion(
                $promotion,
                $kwp,
                $panelBrand,
                $panelPower,
                $inverterBrand
            );

            if ($kitMatchPromotion) {
                $finalPrice = $promotion->final_value;
                $isPromotional = true;
            }
        });

        return ['finalPrice' => $finalPrice, 'isPromotional' => $isPromotional];
    }

    private function kitMatchPromotion(
        PromotionalKit $promotion,
        float          $kwp,
        string         $panelBrand,
        string         $panelPower,
        string         $inverterBrand
    ): bool
    {
        if (
            $kwp == $promotion->kwp
            && strtolower($panelBrand == $promotion->panel_brand)
            && strtolower($panelPower == $promotion->panel_power)
            && strtolower($inverterBrand == $promotion->inverter_brand)
        ) {
            return true;
        }

        return false;
    }

    private function format($netProfitValue, $netProfitPercent): array
    {
        return [
            'netProfitValue' => $netProfitValue,
            'netProfitPercent' => $netProfitPercent,
        ];
    }

    private function getPaymentTypeTotalCost(int $paymentType): string
    {
        return match ($paymentType) {
            PaymentTypeEnum::CASH_PAYMENT => TotalCostForCash::class,
            PaymentTypeEnum::CREDIT_CARD => TotalCostForCreditCard::class,
            PaymentTypeEnum::FINANCING => TotalCostForFinancing::class,
            default => throw new \Exception('payment type not exists.')
        };
    }
}
