<?php


use App\Models\Client;
use App\Models\Lead;
use Illuminate\Support\Str;
use Tests\TestCase;

class DestroyLeadTest extends TestCase
{
    public function test_destroy_lead_returns_200_ok_response(): void
    {
        $lead = Lead::factory()->create();
        $uuid = $lead->uuid;

        Client::create([
            'lead_id' => $lead->id
        ]);

        $this->deleteJson("api/leads/$uuid")->assertOk();
        $this->assertDatabaseMissing(Lead::class, ['email' => $lead->email]);
        $this->assertDatabaseMissing(Client::class, ['lead_id' => $lead->id]);
    }
    public function test_destroy_lead_returns_404_not_found_response(): void
    {
        $uuid = Str::uuid();

        $this->getJson("api/leads/$uuid")
            ->assertNotFound();
    }
}
