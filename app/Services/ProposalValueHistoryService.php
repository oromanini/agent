<?php

namespace App\Services;

use App\Builders\ValueHistoryBuilder;
use App\Enums\PaymentTypeEnum;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Address;
use App\Models\Pricing\ExternalConsultantsCommissionCost;
use App\Models\Proposal;
use App\Models\ProposalValueHistory;
use App\Repositories\WorkCostRepository;

class ProposalValueHistoryService
{
    public const BASE_GROSS_PROFIT = 1.6;

    private ProposalValueHistory $valueHistory;

    public function store(array $data, ?bool $isManual = false): int
    {
        $cost = $this->getKitCost(cost: $data['cost'], isManual: $isManual);

        $financingInitialPrice = $this->getFinalPrice(data: $data, isManual: $isManual, paymentType: PaymentTypeEnum::FINANCING);
        $cashInitialPrice = $this->getFinalPrice(data: $data, isManual: $isManual, paymentType: PaymentTypeEnum::CASH_PAYMENT);
        $cardInitialPrice = $this->getFinalPrice(data: $data, isManual: $isManual, paymentType: PaymentTypeEnum::CREDIT_CARD);

        $commissionPercent = $this->commissionPercent();

        $userId = $this->getAuthUserId();

        $this->valueHistory = (new ValueHistoryBuilder())
            ->withKitCost($cost)
            ->withInitialAndFinalPrice($cashInitialPrice, $financingInitialPrice, $cardInitialPrice)
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

        if ($this->canApplyDiscount(data: $data, key: 'discount_percent')) {
            $this->valueHistory = $this->updateWithDiscountPercent(
                discountPercent: $data['discount_percent'],
            );
            $this->valueHistory->update();
            return ['success', 'Alteração de valor aplicada!'];
        }

        $commissionPercent = $this->canApplyDiscount(data: $data, key: 'commission_percentage')
            ? $this->toDecimal($data['commission_percentage'])
            : $this->valueHistory->commissionPercentage()['commission_percentage'];

        $cardCommissionPercent = $this->canApplyDiscount(data: $data, key: 'card_commission_percent')
            ? $this->toDecimal($data['card_commission_percent'])
            : $this->valueHistory->commissionPercentage()['credit_card_commission_percentage'];

        ($this->commissionPercentIsChanged($commissionPercent, $cardCommissionPercent))
        && $this->valueHistory = $this->updateWithCommissionPercent(
            commissionPercent: $commissionPercent,
            cardCommissionPercent: $cardCommissionPercent,
        );

        $this->valueHistory->update();

        return ['success', 'Alteração de valor aplicada!'];
    }

    private function updateWithDiscountPercent(
        float $discountPercent,
    ): ProposalValueHistory {

        $financingInitialPrice = (float) $this->valueHistory->initial_price;
        $cashInitialPrice = (float) $this->valueHistory->cash_initial_price;
        $cardInitialPrice = (float) $this->valueHistory->card_initial_price;

        $this->valueHistory->final_price = $this->calculateFinalPriceWithDiscount($financingInitialPrice, $this->toDecimal($discountPercent));
        $this->valueHistory->cash_final_price = $this->calculateFinalPriceWithDiscount($cashInitialPrice, $this->toDecimal($discountPercent));
        $this->valueHistory->card_final_price = $this->calculateFinalPriceWithDiscount($cardInitialPrice, $this->toDecimal($discountPercent));

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

        $discountBase = 1 - ($this->valueHistory->discount_percent / 100);

        // PREÇO FINAL COM DESCONTO
        $financingPriceWithDiscount = (float) $this->valueHistory->initial_price * $discountBase;
        $cardPriceWithDiscount = (float) $this->valueHistory->card_initial_price * $discountBase;

        // % COMISSÃO CHEIA
        $fullCommissionPercent = $this->commissionPercent()[ExternalConsultantsCommissionCost::STANDARD_KEY];
        $fullCardCommissionPercent = $this->commissionPercent()[ExternalConsultantsCommissionCost::CREDIT_CARD_KEY];

        // VALOR COMISSÃO CHEIA
        $fullCommissionValue = $financingPriceWithDiscount * $fullCommissionPercent;
        $fullCardCommissionValue = $cardPriceWithDiscount * $fullCardCommissionPercent;

        // VALOR COMISSÃO APÓS DESCONTO DA COMISSÃO
        $financingCommissionAfterDiscount = $financingPriceWithDiscount * $commissionPercent;
        $cardCommissionAfterDiscount = $cardPriceWithDiscount * $cardCommissionPercent;

        // DESCONTO DA COMISSÃO
        $financingCommissionDiscountValue = $fullCommissionValue - $financingCommissionAfterDiscount;
        $cardCommissionDiscountValue = $fullCardCommissionValue - $cardCommissionAfterDiscount;

        $this->valueHistory->final_price = $financingPriceWithDiscount - $financingCommissionDiscountValue;
        $this->valueHistory->cash_final_price = $this->valueHistory->final_price;
        $this->valueHistory->card_final_price = $cardPriceWithDiscount - $cardCommissionDiscountValue;

        return $this->valueHistory;
    }

    public function setValueHistoryData(Proposal $proposal, ?bool $isLead = false): array
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

        $state = $proposal->client->addresses->first()->city->state->name;

        $financingTotalCost = new TotalCostForFinancing(
            cost: $proposal->valueHistory->kit_cost,
            panelCount: $proposal->number_of_panels,
            kwp: $proposal->kwp,
            finalValue: $proposal->valueHistory->final_price,
            paymentType: PaymentTypeEnum::FINANCING,
            state: $state,
            isLead: $isLead
        );
        $cashTotalCost = new TotalCostForCash(
            cost: $proposal->valueHistory->kit_cost,
            panelCount: $proposal->number_of_panels,
            kwp: $proposal->kwp,
            finalValue: $proposal->valueHistory->final_price,
            paymentType: PaymentTypeEnum::CASH_PAYMENT,
            state: $state,
            isLead: $isLead
        );
        $cardTotalCost = new TotalCostForCreditCard(
            cost: $proposal->valueHistory->kit_cost,
            panelCount: $proposal->number_of_panels,
            kwp: $proposal->kwp,
            finalValue: $proposal->valueHistory->final_price,
            paymentType: PaymentTypeEnum::CREDIT_CARD,
            state: $state,
            isLead: $isLead
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
            return stringMoneyToFloat($data['final_value']);
        }
        $state = Address::find($data['installation_address'])->city->state->name;

        return (new PricingService())->calculateFinalPrice(
            cost: $data['cost'],
            kwp: (float)$data['kwp'],
            panelCount: $data['panel_count'],
            panelBrand: $data['panel_brand'],
            panelPower: $data['panelPower'],
            inverterBrand: $data['inverterBrand'],
            roofStructure: $data['roofStructure']->value,
            finalValue: $data['cost'] * self::BASE_GROSS_PROFIT,
            paymentType: $paymentType,
            state: $state
        )['finalPrice'];
    }


    private function commissionPercent(?bool $isDecimal = false): array
    {
        $workCost = (new WorkCostRepository())
            ->getWorkCostByClassification(WorkCostClassificationEnum::EXTERNAL_CONSULTANT_COMMISSION);

        return [
            ExternalConsultantsCommissionCost::STANDARD_KEY => $workCost->costs()[ExternalConsultantsCommissionCost::STANDARD_KEY],
            ExternalConsultantsCommissionCost::CREDIT_CARD_KEY => $workCost->costs()[ExternalConsultantsCommissionCost::CREDIT_CARD_KEY],
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
        $defaultCommissionPercent = $this->commissionPercent()[ExternalConsultantsCommissionCost::STANDARD_KEY] * 100;
        $defaultCardCommissionPercent = $this->commissionPercent()[ExternalConsultantsCommissionCost::CREDIT_CARD_KEY] * 100;

        if (
            $defaultCommissionPercent === $commissionPercent
            || $defaultCardCommissionPercent == $cardCommissionPercent
        ) {
            return false;
        }

        return true;
    }

    private function canApplyDiscount(array $data, string $key): bool
    {
        return isset($data[$key])
        && $this->valueHistory->discount_percent !== ($this->toDecimal($data[$key]));
    }

    public function getAuthUserId(): mixed
    {
        return auth()->user()->id;
    }
}
