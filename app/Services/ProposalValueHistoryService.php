<?php

namespace App\Services;

use App\Builders\ValueHistoryBuilder;
use App\Enums\PaymentTypeEnum;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\ExternalConsultantsCommissionCost;
use App\Models\Proposal;
use App\Models\ProposalValueHistory;
use App\Repositories\WorkCostRepository;

class ProposalValueHistoryService
{
    const BASE_GROSS_PROFIT = 1.6;

    private readonly PricingService $pricingService;
    private ProposalValueHistory $valueHistory;

    public function __construct()
    {
        $this->pricingService = new PricingService();
        $this->valueHistory = new ProposalValueHistory();
    }

    public function store($data, bool $isManual): int
    {
        $cost = $this->getKitCost(cost: $data['cost'], isManual: $isManual);

        $financingInitialPrice = $this->getFinalPrice(data: $data, isManual: $isManual, paymentType: PaymentTypeEnum::FINANCING);
        $cashInitialPrice = $this->getFinalPrice(data: $data, isManual: $isManual, paymentType: PaymentTypeEnum::CASH_PAYMENT);
        $cardInitialPrice = $this->getFinalPrice(data: $data, isManual: $isManual, paymentType: PaymentTypeEnum::CREDIT_CARD);

        $commissionPercent = $this->commissionPercent();
        $userId = auth()->user()->id;

        $this->valueHistory = (new ValueHistoryBuilder())
            ->withKitCost($cost)
            ->withInitialAndFinalPrice($financingInitialPrice, $cashInitialPrice, $cardInitialPrice)
            ->withIsPromotional(false)
            ->withCommissionPercent($commissionPercent)
            ->withDiscountPercent(0)
            ->withAuthUser($userId)
            ->build();

        return $this->valueHistory->id;
    }

    public function update(ProposalValueHistory $valueHistory, array $data): array
    {
        $this->valueHistory = $valueHistory;

        isset($data['discount_percent']) && $this->valueHistory =
            $this->updateWithDiscountPercent(
                discountPercent: $data['discount_percent'],
            );

        $this->commissionPercentIsChanged($data['commission_percent'], $data['card_commission_percent'])
        && $this->valueHistory = $this->updateWithCommissionPercent(
            commissionPercent: $data['commission_percent'],
            cardCommissionPercent: $data['card_commission_percent'],
        );

        $this->valueHistory->update();

        return ['success', 'Alteração de valor aplicada!'];
    }

    private function updateWithDiscountPercent(
        float $discountPercent,
    ): ProposalValueHistory {
        $financingInitialPrice = $this->valueHistory->initial_price;
        $cashInitialPrice = $this->valueHistory->cash_initial_price;
        $cardInitialPrice = $this->valueHistory->card_initial_price;

        $discountPercent = $this->toDecimal($discountPercent);

        $this->valueHistory->final_price = $this->calculateFinalPriceWithDiscount($financingInitialPrice, $discountPercent);
        $this->valueHistory->cash_final_price = $this->calculateFinalPriceWithDiscount($cashInitialPrice, $discountPercent);
        $this->valueHistory->card_final_price = $this->calculateFinalPriceWithDiscount($cardInitialPrice, $discountPercent);

        $this->valueHistory->discount_percent = $discountPercent;

        return $this->valueHistory;
    }

    private function toDecimal(float $percent): float
    {
        return $percent / 100;
    }

    private function updateWithCommissionPercent(
        float $commissionPercent,
        float $cardCommissionPercent
    ): ProposalValueHistory {

        $this->valueHistory->commission = $this->setCommission($commissionPercent, $cardCommissionPercent);

        $commissionPercent = $this->toDecimal($commissionPercent);
        $cardCommissionPercent = $this->toDecimal($cardCommissionPercent);

        $financingFinalPrice = $this->valueHistory->final_price;
        $cashFinalPrice = $this->valueHistory->cash_final_price;
        $cardFinalPrice = $this->valueHistory->card_final_price;

        $decimalCommission = $this->commissionPercent(isDecimal: true);

        // COMISSAO C/DESCTO APLICADO
        $financingInitialCommission = $financingFinalPrice * $decimalCommission[ExternalConsultantsCommissionCost::STANDARD_KEY];
        $cashInitialCommission = $cashFinalPrice * $decimalCommission[ExternalConsultantsCommissionCost::STANDARD_KEY];
        $cardInitialCommission = $cardFinalPrice * $decimalCommission[ExternalConsultantsCommissionCost::CREDIT_CARD_KEY];

        // COMISSAO C/ DESCONTO APLICADO + NOVA COMISSAO
        $financingFinalCommission = $financingFinalPrice * $commissionPercent;
        $cashFinalCommission = $cashFinalPrice * $commissionPercent;
        $cardFinalCommission = $cardFinalPrice * $cardCommissionPercent;

        // DIFERENÇA DA COMISSAO C DESCTO - DESCONTO SOBRE COMISSÂO
        $financingDiscountCommission = $financingInitialCommission - $financingFinalCommission;
        $cashDiscountCommission = $cashInitialCommission - $cashFinalCommission;
        $cardDiscountCommission = $cardInitialCommission - $cardFinalCommission;

        $this->valueHistory->final_price = round($financingFinalPrice - $financingDiscountCommission, 2);
        $this->valueHistory->cash_final_price = round($cashFinalPrice - $cashDiscountCommission, 2);
        $this->valueHistory->card_final_price = round($cardFinalPrice - $cardDiscountCommission, 2);

        return $this->valueHistory;
    }

    public function setValueHistoryData(Proposal $proposal): array
    {
        $valueHistory = $proposal->valueHistory;

        $discountPercent = $this->toDecimal($valueHistory->discount_percent);

        $financingDiscountValue = $valueHistory->initial_price * $discountPercent;
        $cashDiscountValue = $valueHistory->cash_initial_price * $discountPercent;
        $cardDiscountValue = $valueHistory->card_initial_price * $discountPercent;

        $financingCalculateBase = $valueHistory->initial_price - $financingDiscountValue;
        $cashCalculateBase = $valueHistory->cash_initial_price - $cashDiscountValue;
        $cardCalculateBase = $valueHistory->card_initial_price - $cardDiscountValue;

        $initialFinancingCommission = $financingCalculateBase * $this->commissionPercent()[ExternalConsultantsCommissionCost::STANDARD_KEY];
        $initialCashCommission = $cashCalculateBase * $this->commissionPercent()[ExternalConsultantsCommissionCost::STANDARD_KEY];
        $initialCardCommission = $cardCalculateBase * $this->commissionPercent()[ExternalConsultantsCommissionCost::CREDIT_CARD_KEY];

        $finalFinancingCommission = $proposal->valueHistory->final_price * jsonToArray($proposal->valueHistory->commission)[ExternalConsultantsCommissionCost::STANDARD_KEY];
        $finalCashCommission = $proposal->valueHistory->cash_final_price * jsonToArray($proposal->valueHistory->commission)[ExternalConsultantsCommissionCost::STANDARD_KEY];
        $finalCardCommission = $proposal->valueHistory->card_final_price * jsonToArray($proposal->valueHistory->commission)[ExternalConsultantsCommissionCost::CREDIT_CARD_KEY];

        $financingCommissionDiscountValue = $initialFinancingCommission - $finalFinancingCommission;
        $cashCommissionDiscountValue = $initialCashCommission - $finalCashCommission;
        $cardCommissionDiscountValue = $initialCardCommission - $finalCardCommission;

        $financingGrossProfit = ($valueHistory->final_price / $proposal->valueHistory->kit_cost) - 1;
        $cashGrossProfit = ($valueHistory->cash_final_price / $proposal->valueHistory->kit_cost) - 1;
        $cardGrossProfit = ($valueHistory->card_final_price / $proposal->valueHistory->kit_cost) - 1;

        $financingTotalCost = new TotalCostForFinancing(
            cost: $proposal->valueHistory->kit_cost,
            panelCount: $proposal->number_of_panels,
            kwp: $proposal->kwp,
            finalValue: $proposal->valueHistory->final_price,
            paymentType: PaymentTypeEnum::FINANCING
        );
        $cashTotalCost = new TotalCostForCash(
            cost: $proposal->valueHistory->kit_cost,
            panelCount: $proposal->number_of_panels,
            kwp: $proposal->kwp,
            finalValue: $proposal->valueHistory->final_price,
            paymentType: PaymentTypeEnum::CASH_PAYMENT
        );
        $cardTotalCost = new TotalCostForCreditCard(
            cost: $proposal->valueHistory->kit_cost,
            panelCount: $proposal->number_of_panels,
            kwp: $proposal->kwp,
            finalValue: $proposal->valueHistory->final_price,
            paymentType: PaymentTypeEnum::CREDIT_CARD
        );

        return [
            'calculateBase' => $proposal->valueHistory->final_price - $financingDiscountValue,
            'financingDiscountValue' => $financingDiscountValue,
            'cashDiscountValue' => $cashDiscountValue,
            'cardDiscountValue' => $cardDiscountValue,
            'financingInitialValue' => $valueHistory->initial_price,
            'cashInitialValue' => $valueHistory->cash_initial_price,
            'cardInitialValue' => $valueHistory->card_initial_price,
            'initialCommission' => $initialFinancingCommission,
            'initialCashCommission' => $initialCashCommission,
            'initialCardCommission' => $initialCardCommission,
            'finalFinancingCommission' => $finalFinancingCommission,
            'finalCashCommission' => $finalCashCommission,
            'finalCardCommission' => $finalCardCommission,
            'financingCommissionDiscountValue' => $financingCommissionDiscountValue,
            'cashCommissionDiscountValue' => $cashCommissionDiscountValue,
            'cardCommissionDiscountValue' => $cardCommissionDiscountValue,
            'financingGrossProfit' => $financingGrossProfit,
            'cashGrossProfit' => $cashGrossProfit,
            'cardGrossProfit' => $cardGrossProfit,
            'kitCost' => floatToMoney($proposal->valueHistory->kit_cost),
            'financingTotalCost' => $financingTotalCost,
            'cashTotalCost' => $cashTotalCost,
            'cardTotalCost' => $cardTotalCost,

        ];
    }

    private function getKitCost(string $cost, bool $isManual): float
    {
        return $isManual
            ? stringMoneyToFloat($cost)
            : $cost;
    }

    private function getFinalPrice(array $data, bool $isManual, int $paymentType): float|array
    {
        if ($isManual) {
            return stringMoneyToFloat($data['finalValue']);
        }

        return $this->pricingService->calculateFinalPrice(
            cost: $data['cost'],
            kwp: (float)$data['kwp'],
            panelCount: $data['panel_count'],
            panelBrand: $data['panel_brand'],
            panelPower: $data['panel_power'],
            inverterBrand: $data['inverter_brand'],
            roofStructure: $data['roof_structure']->value,
            finalValue: $data['cost'] * self::BASE_GROSS_PROFIT,
            paymentType: $paymentType
        )['finalPrice'];
    }


    private function commissionPercent(?bool $isDecimal = false): array
    {
        $workCost = (new WorkCostRepository())
            ->getWorkCostByClassification(WorkCostClassificationEnum::EXTERNAL_CONSULTANT_COMMISSION);

        return [
            ExternalConsultantsCommissionCost::STANDARD_KEY => ($isDecimal ? 1 : 100) * $workCost->costs()[ExternalConsultantsCommissionCost::STANDARD_KEY],
            ExternalConsultantsCommissionCost::CREDIT_CARD_KEY => ($isDecimal ? 1 : 100) * $workCost->costs()[ExternalConsultantsCommissionCost::CREDIT_CARD_KEY],
        ];
    }

    private function setCommission(float $commissionPercent, float $cardCommissionPercent): bool|string
    {
        return json_encode([
            ExternalConsultantsCommissionCost::STANDARD_KEY => $commissionPercent,
            ExternalConsultantsCommissionCost::CREDIT_CARD_KEY => $cardCommissionPercent,
        ]);
    }

    public function calculateFinalPriceWithDiscount(float $initialPrice, float $discount): float
    {
        return round($initialPrice * (1 - $discount), 2);
    }

    private function commissionPercentIsChanged(float $commissionPercent, float $cardCommissionPercent): bool
    {
        $defaultCommissionPercent = $this->commissionPercent()[ExternalConsultantsCommissionCost::STANDARD_KEY];
        $defaultCardCommissionPercent = $this->commissionPercent()[ExternalConsultantsCommissionCost::CREDIT_CARD_KEY];

        if (
            $defaultCommissionPercent === $commissionPercent
            || $defaultCardCommissionPercent == $cardCommissionPercent
        ) {
            return false;
        }

        return true;
    }
}
