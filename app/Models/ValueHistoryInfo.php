<?php

namespace App\Models;

use App\Enums\PaymentTypeEnum;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\CardFeeCost;
use App\Models\Pricing\DirectCurrentCost;
use App\Models\Pricing\ExternalConsultantsCommissionCost;
use App\Models\Pricing\HomologationCost;
use App\Models\Pricing\InstallationCost;
use App\Models\Pricing\InternalCommercialCommissionCost;
use App\Models\Pricing\InternalFinancialCommissionCost;
use App\Models\Pricing\RoyaltyCost;
use App\Models\Pricing\SafetyMarginCost;
use App\Models\Pricing\TaxCost;
use App\Models\Pricing\WorkCost;
use App\Models\Pricing\WorkMonitoringCost;
use App\Repositories\WorkCostRepository;
use App\Services\TotalCostForCash;
use App\Services\TotalCostForCreditCard;
use App\Services\TotalCostForFinancing;

class ValueHistoryInfo
{
    public function __construct(private readonly Proposal $proposal)
    {
    }

    public function pricingInfo(): static
    {
        $this->setKitCost()
            ->setCashCosts()
            ->setFinancingCosts()
            ->setCardCosts();

        return $this;
    }

    private function setCashCosts(): static
    {
        $this->setTotalCostForCash();
        $this->setInitialAndFinalPrice(PaymentTypeEnum::CASH_PAYMENT);
        $this->setInstallationCost(PaymentTypeEnum::CASH_PAYMENT);
        $this->setHomologationCost(PaymentTypeEnum::CASH_PAYMENT);
        $this->setDirectCurrentCost(PaymentTypeEnum::CASH_PAYMENT);
        $this->setWorkMonitoringCost(PaymentTypeEnum::CASH_PAYMENT);
        $this->setTaxCost(PaymentTypeEnum::CASH_PAYMENT);
        $this->setRoyaltyCost(PaymentTypeEnum::CASH_PAYMENT);
        $this->setSafetyMarginCost(PaymentTypeEnum::CASH_PAYMENT);
        $this->setExternalCommission(PaymentTypeEnum::CASH_PAYMENT);
        $this->setInitialExternalCommission(PaymentTypeEnum::CASH_PAYMENT);
        $this->setInternalCommission(PaymentTypeEnum::CASH_PAYMENT);
        $this->setCommissionDiscount(PaymentTypeEnum::CASH_PAYMENT);
        $this->setDiscount(PaymentTypeEnum::CASH_PAYMENT);

        return $this;
    }

    private function setFinancingCosts(): static
    {
        $this->setTotalCostForFinancing();
        $this->setInitialAndFinalPrice(PaymentTypeEnum::FINANCING);
        $this->setInstallationCost(PaymentTypeEnum::FINANCING);
        $this->setHomologationCost(PaymentTypeEnum::FINANCING);
        $this->setDirectCurrentCost(PaymentTypeEnum::FINANCING);
        $this->setWorkMonitoringCost(PaymentTypeEnum::FINANCING);
        $this->setTaxCost(PaymentTypeEnum::FINANCING);
        $this->setRoyaltyCost(PaymentTypeEnum::FINANCING);
        $this->setSafetyMarginCost(PaymentTypeEnum::FINANCING);
        $this->setInitialExternalCommission(PaymentTypeEnum::FINANCING);
        $this->setExternalCommission(PaymentTypeEnum::FINANCING);
        $this->setInternalCommission(PaymentTypeEnum::FINANCING);
        $this->setCommissionDiscount(PaymentTypeEnum::FINANCING);
        $this->setDiscount(PaymentTypeEnum::FINANCING);

        return $this;
    }

    private function setCardCosts(): static
    {
        $this->setTotalCostForCard();
        $this->setInitialAndFinalPrice(PaymentTypeEnum::CREDIT_CARD);
        $this->setInstallationCost(PaymentTypeEnum::CREDIT_CARD);
        $this->setHomologationCost(PaymentTypeEnum::CREDIT_CARD);
        $this->setDirectCurrentCost(PaymentTypeEnum::CREDIT_CARD);
        $this->setWorkMonitoringCost(PaymentTypeEnum::CREDIT_CARD);
        $this->setTaxCost(PaymentTypeEnum::CREDIT_CARD);
        $this->setRoyaltyCost(PaymentTypeEnum::CREDIT_CARD);
        $this->setSafetyMarginCost(PaymentTypeEnum::CREDIT_CARD);
        $this->setExternalCommission(PaymentTypeEnum::CREDIT_CARD);
        $this->setInitialExternalCommission(PaymentTypeEnum::CREDIT_CARD);
        $this->setInternalCommission(PaymentTypeEnum::CREDIT_CARD);
        $this->setCardFee(PaymentTypeEnum::CREDIT_CARD);
        $this->setCommissionDiscount(PaymentTypeEnum::CREDIT_CARD);
        $this->setCardFinalPriceWithFee();

        return $this;
    }

    private function setKitCost(): static
    {
        $this->kitCost = $this->proposal->valueHistory->kit_cost;
        return $this;
    }

    private function setTotalCostForFinancing(): void
    {
        $this->financing['totalCost'] =
            (new TotalCostForFinancing(
                cost: $this->proposal->valueHistory->kit_cost,
                panelCount: $this->proposal->number_of_panels,
                kwp: $this->proposal->kwp,
                finalValue: $this->proposal->valueHistory->final_price,
                paymentType: PaymentTypeEnum::FINANCING
            ))->cost();
    }

    private function setTotalCostForCash(): void
    {
        $this->cash['totalCost'] =
            (new TotalCostForCash(
                cost: $this->proposal->valueHistory->kit_cost,
                panelCount: $this->proposal->number_of_panels,
                kwp: $this->proposal->kwp,
                finalValue: $this->proposal->valueHistory->final_price,
                paymentType: PaymentTypeEnum::CASH_PAYMENT
            ))->cost();
    }

    private function setTotalCostForCard(): void
    {
        $this->card['totalCost'] =
            (new TotalCostForCreditCard(
                cost: $this->proposal->valueHistory->kit_cost,
                panelCount: $this->proposal->number_of_panels,
                kwp: $this->proposal->kwp,
                finalValue: $this->proposal->valueHistory->final_price,
                paymentType: PaymentTypeEnum::CREDIT_CARD
            ))->cost();
    }

    private function setInstallationCost(int $paymentType): void
    {
        $attribute = $this->getAttributeByPaymentType($paymentType);

        $cost = (new InstallationCost($this->proposal->number_of_panels))->cost();
        $this->{$attribute}["installation"] = $cost;
    }

    private function setHomologationCost(int $paymentType): void
    {
        $attribute = $this->getAttributeByPaymentType($paymentType);

        $cost = (new HomologationCost($this->proposal->kwp))->cost();
        $this->{$attribute}["homologation"] = $cost;

    }

    private function setDirectCurrentCost(int $paymentType): void
    {
        $attribute = $this->getAttributeByPaymentType($paymentType);
        $finalPriceAttribute = $this->getFinalPriceAttributeByPaymentType($paymentType);
        $finalPrice = (float)$this->proposal->valueHistory->{$finalPriceAttribute};
        $cost = (new DirectCurrentCost($finalPrice))->cost();
        $this->{$attribute}["directCurrent"] = $cost;
    }

    private function setWorkMonitoringCost(int $paymentType): void
    {
        $attribute = $this->getAttributeByPaymentType($paymentType);

        $cost = (new WorkMonitoringCost($this->proposal->kwp))->cost();
        $this->{$attribute}["workMonitoring"] = $cost;
    }


    private function setTaxCost(int $paymentType): void
    {
        $attribute = $this->getAttributeByPaymentType($paymentType);
        $finalValue = $this->proposal->valueHistory
            ->{$this->getFinalPriceAttributeByPaymentType($paymentType)};

        $cost = (new TaxCost(
            costValue: $this->proposal->valueHistory->kit_cost,
            finalValue: (float)$finalValue,
            paymentType: $paymentType
        ))->cost();

        $this->{$attribute}["tax"] = $cost;
    }

    private function setRoyaltyCost(int $paymentType): void
    {
        $attribute = $this->getAttributeByPaymentType($paymentType);
        $finalValue = $this->proposal->valueHistory
            ->{$this->getFinalPriceAttributeByPaymentType($paymentType)};

        $cost = (new RoyaltyCost(
            finalValue: (float)$finalValue,
        ))->cost();

        $this->{$attribute}["royalties"] = $cost;
    }

    private function setSafetyMarginCost(int $paymentType): void
    {
        $attribute = $this->getAttributeByPaymentType($paymentType);
        $finalValue = $this->proposal->valueHistory
            ->{$this->getFinalPriceAttributeByPaymentType($paymentType)};

        $cost = (new SafetyMarginCost(
            finalValue: (float)$finalValue
        ))->cost();

        $this->{$attribute}["safetyMargin"] = $cost;
    }

    private function setInternalCommission(int $paymentType): void
    {
        $attribute = $this->getAttributeByPaymentType($paymentType);

        $financial = (new InternalFinancialCommissionCost(
            finalValue: $this->proposal->valueHistory->{$this->getFinalPriceAttributeByPaymentType($paymentType)},
            paymentType: $paymentType
        ))->cost();

        $commercial = (new InternalCommercialCommissionCost(
            finalValue: $this->proposal->valueHistory->{$this->getFinalPriceAttributeByPaymentType($paymentType)}
        ))->cost();

        $this->{$attribute}["financialInternalCommission"] = $financial;
        $this->{$attribute}["commercialInternalCommission"] = $commercial;
    }

    private function setExternalCommission(int $paymentType): void
    {
        $attribute = $this->getAttributeByPaymentType($paymentType);
        $commission = $this->proposal->valueHistory->commissionPercentage();
        $discountBase = 1 - ($this->proposal->valueHistory->discount_percent / 100);

        // PREÇO FINAL COM DESCONTO
        $financingPriceWithDiscount = (float) $this->proposal->valueHistory->initial_price * $discountBase;

        $cost = $paymentType === PaymentTypeEnum::CREDIT_CARD
            ? $financingPriceWithDiscount * $commission['credit_card_commission_percentage']
            : $financingPriceWithDiscount * $commission['commission_percentage'];

        $this->{$attribute}["externalCommission"] = $cost;
    }

    private function setCardFee(int $paymentType)
    {
        $attribute = $this->getAttributeByPaymentType($paymentType);

        $cost = (new CardFeeCost(
            finalValue: $this->card['finalPrice'],
            paymentType: $paymentType
        ))->cost();

        $this->{$attribute}["cardFee"] = $cost;
    }

    private function setInitialAndFinalPrice(int $paymentType): void
    {
        $attribute = $this->getAttributeByPaymentType($paymentType);

        $this->{$attribute}["finalPrice"] = (float) $this->proposal->valueHistory
            ->{$this->getFinalPriceAttributeByPaymentType($paymentType)};

        $this->{$attribute}["initialPrice"] = (float) $this->proposal->valueHistory
            ->{$this->getInitialPriceAttributeByPaymentType($paymentType)};

        if ($paymentType === PaymentTypeEnum::CREDIT_CARD) {
            $priceWithDiscount = $this->financing["initialPrice"]
                - ($this->financing["initialPrice"] * ($this->proposal->valueHistory->discount_percent / 100));

            $workCost = (new WorkCostRepository())
                ->getWorkCostByClassification(
                    WorkCostClassificationEnum::EXTERNAL_CONSULTANT_COMMISSION
                );

            $finalCommissionPercent = $this->proposal->valueHistory->commissionPercentage()['credit_card_commission_percentage'];

            $initialCommission = $workCost->costs()[ExternalConsultantsCommissionCost::CREDIT_CARD_KEY];
            $commissionDiscount = ($priceWithDiscount * $initialCommission) - ($priceWithDiscount * $finalCommissionPercent);

            $this->{$attribute}["finalPrice"] = $priceWithDiscount - $commissionDiscount;
        }
    }

    private function getAttributeByPaymentType(int $paymentType): string
    {
        return match ($paymentType) {
            PaymentTypeEnum::CASH_PAYMENT => 'cash',
            PaymentTypeEnum::FINANCING => 'financing',
            PaymentTypeEnum::CREDIT_CARD => 'card',
            default => throw new \Exception('Invalid payment Type')
        };
    }

    private function getFinalPriceAttributeByPaymentType(int $paymentType): string
    {
        return match ($paymentType) {
            PaymentTypeEnum::CASH_PAYMENT => 'cash_final_price',
            PaymentTypeEnum::FINANCING => 'final_price',
            PaymentTypeEnum::CREDIT_CARD => 'card_final_price',
            default => throw new \Exception('Invalid payment Type')
        };
    }

    private function getInitialPriceAttributeByPaymentType(int $paymentType): string
    {
        return match ($paymentType) {
            PaymentTypeEnum::CASH_PAYMENT => 'cash_initial_price',
            PaymentTypeEnum::FINANCING => 'initial_price',
            PaymentTypeEnum::CREDIT_CARD => 'card_initial_price',
            default => throw new \Exception('Invalid payment Type')
        };
    }

    private function setInitialExternalCommission(int $paymentType): void
    {
        $attribute = $this->getAttributeByPaymentType($paymentType);
        $discountPercent = $this->proposal->valueHistory->discount_percent / 100;

        $priceWithDiscount = $this->cash["initialPrice"]
            - ($this->cash["initialPrice"] * $discountPercent);

        $workCost = (new WorkCostRepository())
            ->getWorkCostByClassification(
                WorkCostClassificationEnum::EXTERNAL_CONSULTANT_COMMISSION
            );

        $commission = $paymentType === PaymentTypeEnum::CREDIT_CARD
            ? $workCost->costs()[ExternalConsultantsCommissionCost::CREDIT_CARD_KEY]
            : $workCost->costs()[ExternalConsultantsCommissionCost::STANDARD_KEY];

        $initialCommission = $priceWithDiscount * $commission;

        $this->{$attribute}["InitialExternalCommission"] = $initialCommission;
    }

    private function setCommissionDiscount(int $paymentType): void
    {
        $attribute = $this->getAttributeByPaymentType($paymentType);
        $initialCommission = $this->{$attribute}["InitialExternalCommission"];
        $finalCommission = $this->{$attribute}["externalCommission"];

        $this->{$attribute}["commissionDiscount"] = $initialCommission - $finalCommission;
    }

    private function setDiscount(int $paymentType): void
    {
        $attribute = $this->getAttributeByPaymentType($paymentType);
        $initialPrice = $this->{$attribute}["initialPrice"];

        $discountValue = $initialPrice * ($this->proposal->valueHistory->discount_percent / 100);

        $this->{$attribute}["defaultDiscount"] = $discountValue;
    }

    private function setCardFinalPriceWithFee(): void
    {
        $this->card["finalPriceWithFee"] = $this->card["finalPrice"] + $this->card["cardFee"];
        $this->card["finalPriceWithFee"] = $this->card["finalPrice"] + $this->card["cardFee"];
        $this->card["finalPriceWithFee"] = $this->card["finalPrice"] + $this->card["cardFee"];
    }
}
