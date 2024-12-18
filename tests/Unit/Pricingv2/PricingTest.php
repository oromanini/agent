<?php

namespace Tests\Unit\Pricingv2;

use App\Builders\WorkCostBuilder;
use App\Enums\InverterBrands;
use App\Enums\PaymentTypeEnum;
use App\Enums\RoofStructure;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\CardFeeCost;
use App\Models\Pricing\DeliveryCost;
use App\Models\Pricing\DirectCurrentCost;
use App\Models\Pricing\ExternalConsultantsCommissionCost;
use App\Models\Pricing\HomologationCost;
use App\Models\Pricing\InstallationCost;
use App\Models\Pricing\InternalCommercialCommissionCost;
use App\Models\Pricing\InternalFinancialCommissionCost;
use App\Models\Pricing\RoyaltyCost;
use App\Models\Pricing\SafetyMarginCost;
use App\Models\Pricing\TaxCost;
use App\Models\Pricing\WorkMonitoringCost;
use App\Packages\EdeltecApiPackage\Enums\PanelBrand;
use App\Services\PricingService;
use Tests\TestCase;

class PricingTest extends TestCase
{
    private PricingService $pricingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pricingService = new PricingService();
        $this->createWorkCosts();
    }

    public function testPricing_WithCashPayment_ShouldReturnFinalValueCorrectly(): void
    {
        $finalPrice = $this->pricingService->calculateFinalPrice(
            cost: 10000,
            kwp: 5.56,
            panelCount: 8,
            panelBrand: PanelBrand::RESUN->name,
            panelPower: 0.56,
            inverterBrand: InverterBrands::Saj->name,
            roofStructure: RoofStructure::COLONIAL->value,
            finalValue: 16500,
            paymentType: PaymentTypeEnum::CASH_PAYMENT,
            state: 'PARANÁ'
        );

        dd($finalPrice);
    }

    private function createWorkCosts(): void
    {
        (new WorkCostBuilder())
            ->withCosts([
                DeliveryCost::KEY => 0.05,
                'enabled' => false
            ])
            ->withClassification(WorkCostClassificationEnum::DELIVERY)
            ->withChangeHistory()
            ->build();

        (new WorkCostBuilder())
            ->withCosts([InstallationCost::KEY => 150])
            ->withClassification(WorkCostClassificationEnum::INSTALLATION)
            ->withChangeHistory()
            ->build();

        (new WorkCostBuilder())
            ->withCosts([DirectCurrentCost::KEY => 0.045])
            ->withClassification(WorkCostClassificationEnum::DIRECT_CURRENT_MATERIAL)
            ->withChangeHistory()
            ->build();

        (new WorkCostBuilder())
            ->withCosts([
                ExternalConsultantsCommissionCost::STANDARD_KEY => 0.1,
                ExternalConsultantsCommissionCost::CREDIT_CARD_KEY => 0.08,
            ])
            ->withClassification(WorkCostClassificationEnum::EXTERNAL_CONSULTANT_COMMISSION)
            ->withChangeHistory()
            ->build();

        (new WorkCostBuilder())
            ->withCosts([HomologationCost::KEY => $this->homologationCostRange()])
            ->withClassification(WorkCostClassificationEnum::HOMOLOGATION)
            ->withChangeHistory()
            ->build();

        (new WorkCostBuilder())
            ->withCosts([InternalCommercialCommissionCost::KEY => 0.01])
            ->withClassification(WorkCostClassificationEnum::INTERNAL_COMMERCIAL_COMMISSION)
            ->withChangeHistory()
            ->build();

        (new WorkCostBuilder())
            ->withCosts([InternalFinancialCommissionCost::KEY => 0.01])
            ->withClassification(WorkCostClassificationEnum::INTERNAL_FINANCING_COMMISSION)
            ->withChangeHistory()
            ->build();

        (new WorkCostBuilder())
            ->withCosts([RoyaltyCost::KEY => 0.02])
            ->withClassification(WorkCostClassificationEnum::ROYALTY)
            ->withChangeHistory()
            ->build();

        (new WorkCostBuilder())
            ->withCosts([SafetyMarginCost::KEY => 0.015])
            ->withClassification(WorkCostClassificationEnum::SAFETY_MARGIN)
            ->withChangeHistory()
            ->build();

        (new WorkCostBuilder())
            ->withCosts([
                TaxCost::SELL_KEY => 0.06,
                TaxCost::SERVICE_KEY => 0.135,
            ])
            ->withClassification(WorkCostClassificationEnum::TAX)
            ->withChangeHistory()
            ->build();

        (new WorkCostBuilder())
            ->withCosts([WorkMonitoringCost::KEY => $this->workMonitoringCosts()])
            ->withClassification(WorkCostClassificationEnum::WORK_MONITORING)
            ->withChangeHistory()
            ->build();

        (new WorkCostBuilder())
            ->withCosts([CardFeeCost::KEY => 0.11])
            ->withClassification(WorkCostClassificationEnum::CARD_FEE)
            ->withChangeHistory()
            ->build();
    }

    private function homologationCostRange(): array
    {
        return
            [
                5 => 100,
                10 => 200,
                15 => 300
            ];
    }

    private function workMonitoringCosts(): array
    {
        return [
            5 => 160,
            10 => 230,
            15 => 450,
            20 => 620,
        ];
    }
}
