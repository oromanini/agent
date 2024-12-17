<?php

namespace Feature\Proposal;

use App\Services\ProposalService;
use Tests\TestCase;

class ProposalServiceTest extends TestCase
{
    public function testProposalServiceCreate_WithValidParams_ShouldCreateProposal()
    {
        (new ProposalService())->store();
    }
}
