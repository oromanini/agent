<?php

namespace Feature\Pricing;

use App\Builders\AddressBuilder;
use App\Builders\CityBuilder;
use App\Builders\ClientBuilder;
use App\Builders\StateBuilder;
use App\Builders\UserBuilder;
use App\Enums\InverterBrands;
use App\Enums\PanelBrands;
use App\Enums\PaymentTypeEnum;
use App\Enums\RoofStructure;
use App\Enums\TensionPattern;
use App\Models\Address;
use App\Models\City;
use App\Models\Client;
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
use App\Models\ProposalValueHistory;
use App\Models\User;
use App\Models\WorkCost;
use App\Services\PricingService;
use App\Services\ProposalValueHistoryService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\PricingTestCase;

class ValueHistoryTest extends PricingTestCase
{
    use RefreshDatabase;

    protected ProposalValueHistoryService|MockInterface $proposalValueHistoryService;
    protected User $user;
    protected City $city;

    /**
     * @param array $data
     * @param int $paymentType
     * @return mixed
     */
    public function getFinalPrice(array $data, int $paymentType): mixed
    {
        return (new PricingService())->calculateFinalPrice(
            cost: $data['cost'],
            kwp: $data['kwp'],
            panelCount: $data['panel_count'],
            panelBrand: $data['panel_brand'],
            panelPower: $data['panelPower'],
            inverterBrand: $data['inverterBrand'],
            roofStructure: RoofStructure::COLONIAL->value,
            finalValue: $data['cost'] * 1.6,
            paymentType: $paymentType,
            state: $this->city->state,
            isLead: false
        )['finalPrice'];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->proposalValueHistoryService = $this->partialMock(
            ProposalValueHistoryService::class,
            function (MockInterface $mock) {
            $mock->shouldReceive('getAuthUserId')
                ->andReturn(1);
        });

        $this->city = $this->getCity();
        $this->user = $this->getUser();
        $this->createWorkCosts();
    }

    public function testProposalValueHistoryCash_WithAutoProposal_ShouldReturnCorrectPrice(): void
    {
        $data = $this->getData();
        $paymentType = PaymentTypeEnum::CASH_PAYMENT;
        $finalValue = $this->getFinalPrice($data, $paymentType);

        $this->proposalValueHistoryService->store(data: $data);

        $installationCost = (new InstallationCost(
            panelQuantity: $this->getData()['panel_count'],
            isLead: false,
            state: $this->city->state
        ))->cost();

        $tax = (new TaxCost(
            costValue:  $data['cost'],
            finalValue: $finalValue,
            paymentType: $paymentType
        ))->cost();

        $consultantCommission = (new ExternalConsultantsCommissionCost(
            finalValue: $finalValue,
            paymentType: $paymentType,
            isLead: false
        ))->cost();

        $delivery = (new DeliveryCost($data['cost'], $this->city->state))->cost();
        $homologationCost = (new HomologationCost($data['kwp']))->cost();
        $workMonitoringCost = (new WorkMonitoringCost($data['kwp']))->cost();
        $safetyMargin = (new SafetyMarginCost($finalValue))->cost();
        $royalty = (new RoyaltyCost($finalValue))->cost();
        $ca = (new DirectCurrentCost($finalValue, false))->cost();
        $internalCommission = (new InternalCommercialCommissionCost(finalValue: $finalValue, isLead: false))->cost();

        //ASSERT DA INSTALAÇÃO
        $this->assertEquals(expected: 130 * $data['panel_count'], actual: $installationCost);

        // ASSERT DO FRETE
        $this->assertEquals(expected: 0, actual: $delivery);

        // ASSERT DO HOMOLOGAÇÃO
        $this->assertEquals(expected: 500, actual: $homologationCost);

        // ASSERT DO MONITORAMENTO
        $this->assertEquals(expected: 1, actual: $workMonitoringCost);

        // ASSERT DA MARGEM DE SEGURANÇA
        $this->assertEquals(expected: $finalValue * 0.017, actual: $safetyMargin);

        // ASSERT DO ROYALTY
        $this->assertEquals(expected: $finalValue * 0.02, actual: $royalty);

        // ASSERT DO IMPOSTO
        $this->assertEquals(expected: $finalValue * 0.03, actual: $tax);

        // ASSERT DO C.A
        $this->assertEquals(expected: 1000, actual: $ca);

        // ASSERT DA COMISSAO VENDEDOR
        $this->assertEquals(expected: $finalValue * 0.12, actual: $consultantCommission);

        // ASSERT DA COMISSAO INTERNA
        $this->assertEquals(expected: $finalValue * 0.01, actual: $internalCommission);
    }

    public function testProposalValueHistoryCreditCard_WithAutoProposal_ShouldReturnCorrectPrice(): void
    {
        $data = $this->getData();
        $paymentType = PaymentTypeEnum::CREDIT_CARD;
        $cashFinalValue = $this->getFinalPrice($data, PaymentTypeEnum::CASH_PAYMENT);

        $this->proposalValueHistoryService->store(data: $data);

        $installationCost = (new InstallationCost(
            panelQuantity: $this->getData()['panel_count'],
            isLead: false,
            state: $this->city->state
        ))->cost();

        $tax = (new TaxCost(
            costValue:  $data['cost'],
            finalValue: $cashFinalValue,
            paymentType: $paymentType
        ))->cost();

        $consultantCommission = (new ExternalConsultantsCommissionCost(
            finalValue: $cashFinalValue,
            paymentType: $paymentType,
            isLead: false
        ))->cost();

        $delivery = (new DeliveryCost($data['cost'], $this->city->state))->cost();
        $homologationCost = (new HomologationCost($data['kwp']))->cost();
        $workMonitoringCost = (new WorkMonitoringCost($data['kwp']))->cost();
        $safetyMargin = (new SafetyMarginCost($cashFinalValue))->cost();
        $royalty = (new RoyaltyCost($cashFinalValue))->cost();
        $ca = (new DirectCurrentCost($cashFinalValue, false))->cost();
        $internalCommission = (new InternalCommercialCommissionCost(finalValue: $cashFinalValue, isLead: false))->cost();

        //ASSERT DA INSTALAÇÃO
        $this->assertEquals(expected: 130 * $data['panel_count'], actual: $installationCost);

        // ASSERT DO FRETE
        $this->assertEquals(expected: 0, actual: $delivery);

        // ASSERT DO HOMOLOGAÇÃO
        $this->assertEquals(expected: 500, actual: $homologationCost);

        // ASSERT DO MONITORAMENTO
        $this->assertEquals(expected: 1, actual: $workMonitoringCost);

        // ASSERT DA MARGEM DE SEGURANÇA
        $this->assertEquals(expected: $cashFinalValue * 0.017, actual: $safetyMargin);

        // ASSERT DO ROYALTY
        $this->assertEquals(expected: $cashFinalValue * 0.02, actual: $royalty);

        // ASSERT DO IMPOSTO
        $this->assertEquals(expected: ($cashFinalValue - $data['cost']) * 0.03, actual: $tax);

        // ASSERT DO C.A
        $this->assertEquals(expected: 1000, actual: $ca);

        // ASSERT DA COMISSAO VENDEDOR
        $this->assertEquals(expected: $cashFinalValue * 0.12, actual: $consultantCommission);

        // ASSERT DA COMISSAO INTERNA
        $this->assertEquals(expected: $cashFinalValue * 0.01, actual: $internalCommission);
    }

    public function testProposalValueHistoryFinancing_WithAutoProposal_ShouldReturnCorrectPrice(): void
    {
        $data = $this->getData();
        $paymentType = PaymentTypeEnum::FINANCING;
        $cashFinalValue = $this->getFinalPrice($data, PaymentTypeEnum::CASH_PAYMENT);

        $this->proposalValueHistoryService->store(data: $data);

        $installationCost = (new InstallationCost(
            panelQuantity: $this->getData()['panel_count'],
            isLead: false,
            state: $this->city->state
        ))->cost();

        $tax = (new TaxCost(
            costValue:  $data['cost'],
            finalValue: $cashFinalValue,
            paymentType: $paymentType
        ))->cost();

        $consultantCommission = (new ExternalConsultantsCommissionCost(
            finalValue: $cashFinalValue,
            paymentType: $paymentType,
            isLead: false
        ))->cost();

        $delivery = (new DeliveryCost($data['cost'], $this->city->state))->cost();
        $homologationCost = (new HomologationCost($data['kwp']))->cost();
        $workMonitoringCost = (new WorkMonitoringCost($data['kwp']))->cost();
        $safetyMargin = (new SafetyMarginCost($cashFinalValue))->cost();
        $royalty = (new RoyaltyCost($cashFinalValue))->cost();
        $ca = (new DirectCurrentCost($cashFinalValue, false))->cost();
        $internalCommission = (new InternalCommercialCommissionCost(finalValue: $cashFinalValue, isLead: false))->cost();
        $financingCommission = (new InternalFinancialCommissionCost(finalValue: $cashFinalValue, paymentType: $paymentType))->cost();

        //ASSERT DA INSTALAÇÃO
        $this->assertEquals(expected: 130 * $data['panel_count'], actual: $installationCost);

        // ASSERT DO FRETE
        $this->assertEquals(expected: 0, actual: $delivery);

        // ASSERT DO HOMOLOGAÇÃO
        $this->assertEquals(expected: 500, actual: $homologationCost);

        // ASSERT DO MONITORAMENTO
        $this->assertEquals(expected: 1, actual: $workMonitoringCost);

        // ASSERT DA MARGEM DE SEGURANÇA
        $this->assertEquals(expected: $cashFinalValue * 0.017, actual: $safetyMargin);

        // ASSERT DO ROYALTY
        $this->assertEquals(expected: $cashFinalValue * 0.02, actual: $royalty);

        // ASSERT DO IMPOSTO
        $this->assertEquals(expected: ($cashFinalValue - $data['cost']) * 0.135, actual: $tax);

        // ASSERT DO C.A
        $this->assertEquals(expected: 1000, actual: $ca);

        // ASSERT DA COMISSAO VENDEDOR
        $this->assertEquals(expected: $cashFinalValue * 0.12, actual: $consultantCommission);

        // ASSERT DA COMISSAO INTERNA
        $this->assertEquals(expected: $cashFinalValue * 0.01, actual: $internalCommission);

        // ASSERT DA COMISSAO FINANCIAMENTO
        $this->assertEquals(expected: $cashFinalValue * 0.01, actual: $internalCommission);
    }

    public function testProposalValueHistoryCash_WithAutoProposal_ShouldAssertHistory(): void
    {
        $data = $this->getData();

        $cashFinalValue = $this->getFinalPrice($data, PaymentTypeEnum::CASH_PAYMENT);
        $financingFinalValue = $this->getFinalPrice($data, PaymentTypeEnum::FINANCING);
        $cardFinalValue = $this->getFinalPrice($data, PaymentTypeEnum::CREDIT_CARD);

        $id = $this->proposalValueHistoryService->store(data: $data);
        $proposalValueHistory = ProposalValueHistory::find($id);

        $this->assertEquals(expected: $cashFinalValue, actual: $proposalValueHistory['cash_initial_price']);
        $this->assertEquals(expected: $cardFinalValue, actual: $proposalValueHistory['card_initial_price']);
        $this->assertEquals(expected: $financingFinalValue, actual: $proposalValueHistory['initial_price']);
    }

    public function testProposalValueHistory_WithManualProposal_ShouldReturnCorrectPrice(): void
    {
        $this->proposalValueHistoryService->store(
            data: $this->getData(),
            isManual: true
        );
    }

    public function getData(): array
    {
        $client = $this->getClient();

        return [
          "client"               => $client,
          "average_consumption"  => 1000,
          "kw_price"             => 1,
          "tension_pattern"      => TensionPattern::MONOFASICO_220V,
          "installation_address" => $this->getInstallationAddress($client)->id,
          "installation_uc"      => null,
          "agent"                => $this->user,
          "roof_structure"       => RoofStructure::COLONIAL,
          "orientation"          => "norte",
          "kit_id"               => "decc69b4-c8eb-4686-beb2-6e2f50498a92",
          "cost"                 => 8000.00,
          "kwp"                  => 6,
          "panel_count"          => 10,
          "panel_brand"          => strtoupper(PanelBrands::Nplus->name),
          "panelPower"           => 600,
          "inverterBrand"        => strtoupper(InverterBrands::Saj->name),
          "roofStructure"        => RoofStructure::COLONIAL,
        ];
    }

    private function getClient(): Client
    {
        return (new ClientBuilder())
            ->withNameOrCompanyName('João')
            ->withAgentId($this->user)
            ->withDocument('000.000.000-00')
            ->withPhoneNumber('(44)99999-9999')
            ->build();
    }

    public function getInstallationAddress(Client $client): Address
    {
        return (new AddressBuilder())
            ->withStreet('street')
            ->withNumber('10')
            ->withZipcode('00000-000')
            ->withNeighborhood('subusban')
            ->withCity(City::first())
            ->withClient($client)
            ->build();
    }

    private function getUser(): User|Model
    {
        return (new UserBuilder())
            ->withName('Test User')
            ->withEmail('email@email.com')
            ->withPassword('123')
            ->withPhoneNumber('(00)00000-0000')
            ->withCity($this->city->id)
            ->withCpf('000.000.000-00')
            ->withCnpj('00.000.000/0000-00')
            ->withAscendant(0)
            ->build();
    }

    private function getCity(): City|Model
    {
        $state = (new StateBuilder())
            ->withName('TestState')
            ->withRegion('sul')
            ->build();

        return (new CityBuilder())
            ->withName('Test City')
            ->withActive(true)
            ->withLatitude(1)
            ->withLongitude(1)
            ->withFederalUnit('PR')
            ->withStateId($state->id)
            ->build();
    }
}
