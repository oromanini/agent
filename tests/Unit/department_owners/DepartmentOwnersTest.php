<?php

namespace Tests\Unit\department_owners;

use App\Builders\PreInspectionBuilder;
use App\Builders\ProposalBuilder;
use App\Builders\ValueHistoryBuilder;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Financing;
use App\Models\Homologation;
use App\Models\Inspection;
use App\Models\Installation;
use App\Models\Proposal;
use App\Models\Status;
use App\Models\User;
use App\Services\HomologationService;
use Database\Factories\StatusFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Tests\TestCase;

class DepartmentOwnersTest extends TestCase
{
    public function departments(): \Generator
    {
        yield 'inspection' => [new Inspection()];
        yield 'contract' => [new Contract()];
        yield 'financing' => [new Financing()];
        yield 'homologation' => [new Homologation()];
        yield 'installation' => [new Installation()];
    }

    /** @dataProvider departments */
    public function testRelationshipWithDepartmentOwner_shouldReturnOwner(Model $department): void
    {
        $owner = User::factory()->create();
        $subOwner = User::factory()->create();

        $department->owner()->associate($owner);
        $department->secondaryOwner()->associate($subOwner);

        $this->assertTrue($department->owner instanceof User);
        $this->assertTrue($department->secondaryOwner instanceof User);
    }

    /** @dataProvider departments */
    public function testRelationshipWithDepartmentOwner_shouldSaveOwner(Model $department): void
    {
        $proposal = Proposal::factory()->create();
        $owner = User::factory()->create();
        $subOwner = User::factory()->create();
        $status = Status::factory()->create();

        $department->owner()->associate($owner);
        $department->secondaryOwner()->associate($subOwner);

        if ($department instanceof Installation || $department instanceof Homologation) {
            $department->proposal_id = $proposal->id;
            $department->checklist = json_encode([]);
            $department->status_id = $status->id;
        }
        $department->save();

        $this->assertNotEmpty($department::find($department->id));
    }

    public function testAutomaticAttributionToOwner_WhenApprovalIsComplete_ShoultAttibuteTechnicalToHomologationOwnerAndInstallationSecondaryOwner()
    {
        $owner = User::factory(['name' => 'responsavel teste'])->create();
        $inspection = Inspection::factory(['owner_id' => $owner->id])->create();
        $financing = Financing::factory()->create();
        $contract = Contract::factory()->create();
        $client = Client::factory()->create();
        $agent = User::factory()->create();

        $proposal = $this->buildProposalWithApproval($agent, $client, $inspection, $financing, $contract);
        $status = Status::factory(['is_final' => true])->create();

        Status::factory(['department' => 0, 'name' => 'Pendente'])->create();

        $inspection->status()->associate($status);
        $financing->status()->associate($status);
        $contract->status()->associate($status);

        $inspection->update();
        $financing->update();
        $contract->update();

        $this->assertNotNull($proposal->homologation);
        $this->assertEquals($proposal->homologation->owner->id, $owner->id);
    }

    public function testAutomaticAttributionToOwner_WhenHomologationIsComplete_ShoultAttibuteTechnicalToInstallationSecondaryOwner(): void
    {
        $owner = User::factory(['name' => 'responsavel teste'])->create();
        $inspection = Inspection::factory(['owner_id' => $owner->id])->create();
        $financing = Financing::factory()->create();
        $contract = Contract::factory()->create();
        $client = Client::factory()->create();
        $agent = User::factory()->create();
        $proposal = $this->buildProposalWithApproval($agent, $client, $inspection, $financing, $contract);
        $status = Status::factory(['department' => 0, 'name' => 'Pendente'])->create();

        Status::factory(['id'=> 13, 'department' => 0, 'name' => 'trt_payed'])->create();
        Status::factory(['id'=> 15, 'department' => 0, 'name' => 'trt_payed'])->create();
        Status::factory(['id'=> 16, 'department' => 0, 'name' => 'trt_payed'])->create();
        Status::factory(['id'=> 17, 'department' => 0, 'name' => 'trt_payed'])->create();
        Status::factory(['id'=> 18, 'department' => 0, 'name' => 'trt_pay_order'])->create();

        $homologation = Homologation::factory([
            'owner_id' => $owner->id,
            'proposal_id' => $proposal->id,
            'status_id' => $status->id,
            'single_line_project' => 'aosidjaoijsdioasjd'
        ])->create();

        $request = new Request();

        (new HomologationService())->update($homologation, $request);

        $this->assertEquals($proposal->installation->secondary_owner_id, $owner->id);
    }

    public function buildProposalWithApproval(
        User $agent,
        Client $client,
        Inspection $inspection,
        Financing $financing,
        Contract $contract
    ): Proposal {
        $preInspection = (new PreInspectionBuilder())
            ->build();

        $valueHistory = (new ValueHistoryBuilder())
            ->withAuthUser($agent->id)
            ->withCommissionPercent(['commission' => 0.1])
            ->withDiscountPercent(0)
            ->withInitialAndFinalPrice(20000, 22000)
            ->withIsPromotional(false)
            ->withKitCost(10000)
            ->build();

        $proposal = (new ProposalBuilder())
            ->withClient($client)
            ->withAgent($agent)
            ->withInspection($inspection->id)
            ->withFinancing($financing->id)
            ->withContract($contract->id)
            ->withPreInspection($preInspection)
            ->withValueHistory($valueHistory)
            ->withEstimatedGeneration()
            ->withAverageConsumption()
            ->withRoofStructure()
            ->withPanelQuantity()
            ->withKwhPrice()
            ->withComponents()
            ->withKitUuid()
            ->withManualData([])
            ->withKwp(8)
            ->withRoofOrientation()
            ->build();
        return $proposal;
    }
}
