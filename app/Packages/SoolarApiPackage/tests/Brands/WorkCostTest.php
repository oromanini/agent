<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use App\Models\WorkCost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Support\Facades\DB; // Importe o Facade DB

class WorkCostTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    /** @test */
    public function testUpdateWorkCost_WhenValidDataIsProvided_ShouldUpdateCostsAndHistory(): void
    {
        $workCost = WorkCost::factory()->create([
            'classification' => 1,
            'costs' => ['old_value' => 100]
        ]);

        $newCosts = [
            'new_value' => 200,
            'tax_percentage' => '15,5%',
        ];

        $updateData = [
            'classification' => 1,
            'costs' => json_encode($newCosts),
        ];

        $response = $this->putJson("/work-costs/{$workCost->id}", $updateData);

        // A CORREÇÃO MAIS FLEXÍVEL: USANDO DB::raw
        $this->assertDatabaseHas('work_costs', [
            'id' => $workCost->id,
            // Usa JSON_EXTRACT para verificar os valores específicos no JSON.
            // Isso evita a falha causada por diferenças de formatação da string JSON.
            'costs->new_value' => 200,
            'costs->tax_percentage' => 0.155,
        ]);

        $response->assertRedirect('/work-costs');

        $updatedWorkCost = WorkCost::find($workCost->id);
        $history = $updatedWorkCost->change_history;

        $this->assertCount(2, $history);
        $this->assertEquals('updated', $history[1]['action']);
        $this->assertEquals(['old_value' => 100], $history[1]['previous_costs']);
    }

    /** @test */
    public function testUpdateWorkCost_WhenJsonIsInvalid_ShouldReturnValidationError(): void
    {
        $workCost = WorkCost::factory()->create();

        $updateData = [
            'classification' => 1,
            'costs' => '{"invalid_json":,}',
        ];

        $response = $this->putJson("/work-costs/{$workCost->id}", $updateData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('costs');
    }
}
