<?php

namespace Tests\Unit\Pricingv2;

use App\Builders\ClientBuilder;
use App\Builders\PreInspectionBuilder;
use App\Builders\ProposalBuilder;
use App\Builders\ValueHistoryBuilder;
use App\Builders\WorkCostBuilder;
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
use App\Models\Proposal;
use App\Models\ProposalValueHistory;
use App\Models\User;
use App\Models\ValueHistoryInfo;
use App\Services\PricingService;
use App\Services\ProposalValueHistoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class ProposalValueHistoryTest extends TestCase
{
    use RefreshDatabase;

    const BASE_GROSS_PROFIT = 1.6;

    protected function setUp(): void
    {
        parent::setUp();
        $this->valueHistoryService = new ProposalValueHistoryService();
        $this->createWorkCosts();
        $this->user = User::factory()->create();
        Auth::login($this->user);
        env('PROFIT',0.12);
    }

    public function testProposalValueHistoryStore(): void
    {
        $data = $this->getData();
        $id = $this->valueHistoryService->store(data: $data, isManual: false);

        $this->assertModelExists(ProposalValueHistory::find($id));
    }

    public function testProposalValueHistoryUpdate_With3percentDiscount(): void
    {
        $valueHistory = (new ValueHistoryBuilder())
            ->withKitCost(10000)
            ->withInitialAndFinalPrice(19000, 20000)
            ->withIsPromotional(false)
            ->withCommissionPercent([
                ExternalConsultantsCommissionCost::STANDARD_KEY => 0.1,
                ExternalConsultantsCommissionCost::CREDIT_CARD_KEY => 0.08,
            ])
            ->withDiscountPercent(0)
            ->withAuthUser($this->user->id)
            ->build();

        $valueHistoryService = new ProposalValueHistoryService();
        $valueHistoryService->update($valueHistory, [
            'kwp' => 4.2,
            'panel_count' => 8,
            'roof_structure' => RoofStructure::COLONIAL,
            'panel_brand' => 'jinko',
            'panel_power' => 550,
            'inverter_brand' => 'growatt',
            'discount_percent' => 3,
            'commission_percent' => 10,
            'card_commission_percent' => 8,
        ]);

        $attributes = $valueHistory->getAttributes();
        $this->assertEquals(18430, $attributes['final_price']);
        $this->assertEquals(18430, $attributes['cash_final_price']);
        $this->assertEquals(19400, $attributes['card_final_price']);
    }

    public function testProposalValueHistoryUpdate_With1percentCommissionDiscount(): void
    {
        $valueHistory = (new ValueHistoryBuilder())
            ->withKitCost(10000)
            ->withInitialAndFinalPrice(19000, 18000)
            ->withIsPromotional(false)
            ->withCommissionPercent([
                ExternalConsultantsCommissionCost::STANDARD_KEY => 0.1,
                ExternalConsultantsCommissionCost::CREDIT_CARD_KEY => 0.08,
            ])
            ->withDiscountPercent(0)
            ->withAuthUser($this->user->id)
            ->build();

        $valueHistoryService = new ProposalValueHistoryService();
        $valueHistoryService->update($valueHistory, [
            'kwp' => 5.3,
            'panel_count' => 10,
            'roof_structure' => RoofStructure::COLONIAL,
            'panel_brand' => 'jinko',
            'panel_power' => 550,
            'inverter_brand' => 'growatt',
            'discount_percent' => 0,
            'commission_percentage' => 9,
            'card_commission_percent' => 7,
        ]);
        $valueHistory->refresh();
        $attributes = $valueHistory->getAttributes();
        $this->assertEquals(18810, $attributes['final_price']);
        $this->assertEquals(18810, $attributes['cash_final_price']);
        $this->assertEquals(17820, $attributes['card_final_price']);
    }

    public function testProposalValueHistoryUpdate_WithCommissionDiscountAndDefaultDiscount(): void
    {
        $valueHistory = (new ValueHistoryBuilder())
            ->withKitCost(10000)
            ->withInitialAndFinalPrice(19000, 20000)
            ->withIsPromotional(false)
            ->withCommissionPercent([
                ExternalConsultantsCommissionCost::STANDARD_KEY => 0.1,
                ExternalConsultantsCommissionCost::CREDIT_CARD_KEY => 0.08,
            ])
            ->withDiscountPercent(0)
            ->withAuthUser($this->user->id)
            ->build();

        $valueHistoryService = new ProposalValueHistoryService();
        $valueHistoryService->update($valueHistory, [
            'kwp' => 5.3,
            'panel_count' => 10,
            'roof_structure' => RoofStructure::COLONIAL,
            'panel_brand' => 'jinko',
            'panel_power' => 550,
            'inverter_brand' => 'growatt',
            'discount_percent' => 3,
            'commission_percent' => 9,
            'card_commission_percent' => 7,
        ]);

        $attributes = $valueHistory->getAttributes();
        $this->assertEquals(18430, $attributes['final_price']);
        $this->assertEquals(18430, $attributes['cash_final_price']);
        $this->assertEquals(19400, $attributes['card_final_price']);
    }

    public function paymentTypeScenarios(): \Generator
    {
        yield 'cash' => [
            'paymentType' => PaymentTypeEnum::CASH_PAYMENT,
            'finalValue' => 19000,
        ];
        yield 'financing' => [
            'paymentType' => PaymentTypeEnum::FINANCING,
            'finalValue' => 19500
        ];
        yield 'card' => [
            'paymentType' => PaymentTypeEnum::CREDIT_CARD,
            'finalValue' => 21000,
        ];

    }

    /** @dataProvider paymentTypeScenarios */
    public function testPricingService_WitAllPaymentTypes_ShouldReturnThreePrices($paymentType, $finalValue): void
    {
        $finalPrice = (new PricingService())->calculateFinalPrice(
            cost: 10000,
            kwp: 4.2,
            panelCount: 8,
            panelBrand: 'jinko',
            panelPower: 550,
            inverterBrand: 'growatt',
            roofStructure: RoofStructure::COLONIAL->value,
            finalValue: 18000,
            paymentType: $paymentType,
            state: 'PARANÁ'
        );

        $expected = [
            "finalPrice" => $finalValue,
            "isPromotional" => false,
        ];

        $this->assertEquals($expected, $finalPrice);
    }

    public function testValueHistoryInfo(): void
    {
        $valueHistory = (new ValueHistoryBuilder())
            ->withKitCost(10000)
            ->withInitialAndFinalPrice(19000, 18000)
            ->withIsPromotional(false)
            ->withCommissionPercent([
                ExternalConsultantsCommissionCost::STANDARD_KEY => 0.1,
                ExternalConsultantsCommissionCost::CREDIT_CARD_KEY => 0.08,
            ])
            ->withDiscountPercent(0)
            ->withAuthUser($this->user->id)
            ->build();

        $proposal = $this->createProposal($valueHistory);

        $valueHistoryInfo = new ValueHistoryInfo($proposal);

        $this->assertInstanceOf(ValueHistoryInfo::class, $valueHistoryInfo->pricingInfo());
    }

    private function getData(): array
    {
        return [
            'cost' => 10000,
            'kwp' => 5.3,
            'panel_count' => 10,
            'roofStructure' => RoofStructure::COLONIAL,
            'final_value' => 19500,
            'panel_brand' => 'jinko',
            'panelPower' => 550,
            'inverterBrand' => 'growatt',
        ];
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
            ->withCosts([DirectCurrentCost::KEY => 0.05])
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
            ->withCosts([SafetyMarginCost::KEY => 0.017])
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
                10 => 400,
                50 => 500,
                100 => 750
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

    private function createProposal(ProposalValueHistory $valueHistory): Proposal
    {
        $preInspection = (new PreInspectionBuilder())->build();

        $client = (new ClientBuilder())
            ->withAgentId($this->user)
            ->withNameOrCompanyName('Teste')
            ->withAccountOwnerDocument('000.000.000-00')
            ->withOwnerDocument('000.000.000-00')
            ->withDocument('000.000.000-00')
            ->withBirthdate('2003-01-01')
            ->withEmail('teste@teste.com')
            ->withPhoneNumber('(45) 9 9999-9999')
            ->withType('person')
            ->build();

        return (new ProposalBuilder())
            ->isManual(false)
            ->withKwp(4.2)
            ->withManualData(null)
            ->withRoofOrientation()
            ->withAgent($this->user)
            ->withClient($client)
            ->withType()
            ->withValueHistory($valueHistory)
            ->withTensionPattern('MONO-220')
            ->withPreInspection($preInspection)
            ->withKwhPrice(0.80)
            ->withKitUuid(Uuid::uuid4())
            ->withComponents(['1', '2'])
            ->withEstimatedGeneration(500)
            ->withAverageConsumption(450)
            ->withRoofStructure(RoofStructure::COLONIAL->value)
            ->withPanelQuantity(8)
            ->build();
    }
}
