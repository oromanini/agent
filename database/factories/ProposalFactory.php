<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\City;
use App\Models\Client;
use App\Models\ConsumerUnit;
use App\Models\PreInspection;
use App\Models\Proposal;
use App\Models\ProposalValueHistory;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class ProposalFactory extends Factory
{

    protected $model = Proposal::class;

    public function definition(): array
    {
        $client = Client::factory()->create();
        $agent = User::factory()->create();
        $preInspection = PreInspection::factory()->create();
        $proposalValueHistory = ProposalValueHistory::factory()->create();

        return [
            'uuid' => $this->faker->uuid(),
            'kit_uuid' => $this->faker->uuid(),
            'estimated_generation' => 1050,
            'average_consumption' => 1000,
            'roof_structure' => 1,
            'number_of_panels' => 1,
            'kw_price' => 0.89,
            'components' => json_encode([]),
            'roof_orientation' => json_encode(['norte']),
            'manual_data' => json_encode([]),
            'client_id' => $client,
            'agent_id' => $agent,
            'pre_inspection_id' => $preInspection,
            'value_history_id' => $proposalValueHistory,
            'kwp' => 11,
        ];
    }
}
