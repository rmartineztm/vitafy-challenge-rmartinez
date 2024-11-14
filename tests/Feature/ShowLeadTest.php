<?php

namespace Tests\Feature;

use App\Http\Resources\LeadResource;
use App\Models\Lead;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShowLeadTest extends TestCase
{
    public function test_show_lead_returns_200_ok(): void
    {
        $lead = Lead::factory()->create();
        $uuid = $lead->uuid;

        $this->getJson("api/leads/$uuid")
            ->assertOk();
    }

    public function test_show_lead_returns_lead_resource(): void
    {
        $lead = Lead::factory()->create();
        $uuid = $lead->uuid;

        $this->getJson("api/leads/$uuid")
            ->assertExactJson(LeadResource::make($lead)->response()->getData(true));
    }

    public function test_show_lead_returns_correct_data(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'phone' => '0123456789',
            'score' => 250
        ];
        $lead = Lead::create($data);
        $uuid = $lead->uuid;

        $this->getJson("api/leads/$uuid")
            ->assertJson($data);
    }

    public function test_show_lead_with_optional_fields_returns_correct_data(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
        ];
        $lead = Lead::create($data);
        $uuid = $lead->uuid;

        $this->getJson("api/leads/$uuid")
            ->assertJson([
                'name' => 'John Doe',
                'email' => 'john@doe.com',
                'phone' => null,
                'score' => null
            ]);
    }

    public function test_show_lead_returns_404_not_found(): void
    {
        $uuid = Str::uuid();

        $this->getJson("api/leads/$uuid")
            ->assertNotFound();
    }
}
