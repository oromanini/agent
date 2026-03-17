<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgentLandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_can_be_rendered()
    {
        $response = $this->get('/landingpage');

        $response->assertOk();
        $response->assertSee('Seja um Agente Alluz');
    }

    public function test_landing_page_form_creates_crm_agent_lead()
    {
        $response = $this->post('/landingpage', [
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'phone_number' => '11999990000',
            'solar_experience' => 'ate_2_anos',
        ]);

        $response->assertRedirect(route('landingpage.thank-you'));

        $this->assertDatabaseHas('crm_agent_leads', [
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'phone_number' => '11999990000',
            'solar_experience' => 'ate_2_anos',
            'status' => 'novo',
            'created_by' => null,
        ]);
    }
}
