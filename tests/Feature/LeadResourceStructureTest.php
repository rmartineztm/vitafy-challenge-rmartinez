<?php

namespace Tests\Feature;

use App\Http\Resources\LeadResource;
use App\Models\Lead;
use Tests\TestCase;

class LeadResourceStructureTest extends TestCase
{
    public function test_lead_resource_structure(): void
    {
        $this->assertableResource(LeadResource::make(Lead::factory()->create()))
            ->assertStructure([
                'name',
                'email',
                'phone',
                'score',
                'created_at',
                'updated_at'
            ]);
    }
}
