<?php

namespace Tests\Unit\department_owners;

use App\Models\Inspection;
use App\Models\User;
use Tests\TestCase;

class DepartmentOwnersTest extends TestCase
{
    public function testRelationshipWithDepartmentOwner_shouldReturnOwner(): void
    {
        $inspection = new Inspection();
        $user = User::factory()->create();
        $inspection->owner()->associate($user);

        $this->assertTrue($inspection->owner instanceof User);
    }

    public function testRelationshipWithDepartmentOwner_shouldSaveOwner(): void
    {
        $inspection = new Inspection();
        $user = User::factory()->create();
        $inspection->owner()->associate($user);
        $inspection->save();

        $this->assertNotEmpty(Inspection::find($inspection->id));
    }
}
