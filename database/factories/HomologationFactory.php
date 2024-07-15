<?php

namespace Database\Factories;

use App\Models\Homologation;
use Illuminate\Database\Eloquent\Factories\Factory;

class HomologationFactory extends Factory
{
    protected $model = Homologation::class;

    public function definition(): array
    {
        return [
            'proposal_id' => \App\Models\Proposal::factory(),
            'protocol_approval_date' => $this->faker->dateTime(),
            'trt_pay_order' => $this->faker->word,
            'proof_of_bill_payment' => $this->faker->filePath(),
            'access_opinion_form' => $this->faker->filePath(),
            'signed_access_opinion_form' => $this->faker->filePath(),
            'notes' => $this->faker->paragraph,
//            'single_line_project' => $this->faker->filePath(),
//            'is_approved_on_dealership' => $this->faker->randomElement(['Aguardando', 'Em análise', 'Aprovado', 'Reprovado']),
            'checklist' => json_encode([$this->faker->word, $this->faker->word]), // Exemplos de checklist JSON
            'payment_voucher' => $this->faker->text(50),
            'status_id' => \App\Models\Status::factory(),
            'deleted_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'owner_id' => \App\Models\User::factory(),
            'secondary_owner_id' => \App\Models\User::factory(),
        ];
    }
}
