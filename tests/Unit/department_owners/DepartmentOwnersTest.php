<?php

namespace Tests\Unit\department_owners;

use App\Models\Contract;
use App\Models\Financing;
use App\Models\Homologation;
use App\Models\Inspection;
use App\Models\Installation;
use App\Models\Proposal;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
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
}
