<?php

namespace Tests\Feature;

use App\Contracts\LeadScoringServiceInterface;
use App\Http\Resources\LeadResource;
use App\Models\Client;
use App\Models\Lead;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class StoreLeadTest extends TestCase
{
    private array $requiredStoreData;
    private array $optionalStoreData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requiredStoreData = [
            'name' => fake()->name,
            'email' => fake()->email
        ];

        $this->optionalStoreData = [
            'name' => fake()->name,
            'email' => fake()->email,
            'phone' => fake()->phoneNumber
        ];
    }

    public function test_store_lead_with_required_fields_returns_201_created(): void
    {
        $this->postJson('api/leads', $this->requiredStoreData)->assertCreated();
    }

    public function test_store_lead_with_optional_fields_returns_201_created(): void
    {
        $this->postJson('api/leads', $this->optionalStoreData)->assertCreated();
    }

    public function test_store_lead_field_name_is_not_string_returns_422_unprocessable(): void
    {
        $this->requiredStoreData['name'] = 1111111111;

        $this->postJson('api/leads', $this->requiredStoreData)->assertUnprocessable();
    }

    public function test_store_lead_field_name_exceeds_max_255_returns_422_unprocessable(): void
    {
        $this->requiredStoreData['name'] = Str::repeat('a', 256);

        $this->postJson('api/leads', $this->requiredStoreData)->assertUnprocessable();
    }

    public function test_store_lead_field_email_exceeds_max_255_returns_422_unprocessable(): void
    {
        $this->requiredStoreData['email'] = Str::repeat('a', 100) . '@' . Str::repeat('a', 152) . '.com';

        $this->postJson('api/leads', $this->requiredStoreData)->assertUnprocessable();
    }

    public function test_store_lead_field_email_is_not_valid_email_returns_422_unprocessable(): void
    {
        $this->requiredStoreData['email'] = fake()->text();

        $this->postJson('api/leads', $this->requiredStoreData)->assertUnprocessable();
    }

    public function test_store_lead_field_email_duplicate_returns_422_unprocessable(): void
    {
        $email = fake()->email;

        Lead::factory()->create(['email' => $email]);

        $this->requiredStoreData['email'] = $email;

        $this->postJson('api/leads', $this->requiredStoreData)->assertUnprocessable();
    }

    public function test_store_lead_field_phone_exceeds_max_20_returns_422_unprocessable(): void
    {
        $this->optionalStoreData['phone'] = Str::repeat('1', 21);

        $this->postJson('api/leads', $this->optionalStoreData)->assertUnprocessable();
    }

    public function test_store_lead_field_phone_is_not_string_returns_422_unprocessable(): void
    {
        $this->optionalStoreData['phone'] = 1111111111;

        $this->postJson('api/leads', $this->optionalStoreData)->assertUnprocessable();
    }

    public function test_store_lead_with_empty_request_returns_422_unprocessable(): void
    {
        $this->postJson('api/leads')
            ->assertUnprocessable();
    }

    public function test_store_lead_creates_new_client_with_attached_lead()
    {
        $clientsCount = Client::count();

        $this->postJson('api/leads', $this->requiredStoreData);
        $lead = Lead::where('email', $this->requiredStoreData['email'])->first();

        $this->assertDatabaseCount(Client::class, $clientsCount + 1);
        $this->assertEquals($lead->id, Client::latest()->first()->lead_id);
    }

    public function test_store_lead_returns_lead_resource(): void
    {
        $response = $this->postJson('api/leads', $this->requiredStoreData);

        $lead = Lead::where('email', $this->requiredStoreData['email'])->first();

        $response->assertExactJson(LeadResource::make($lead)->response()->getData(true));
    }

    public function test_store_lead_returns_correct_data(): void
    {
        $this->postJson('api/leads', $this->requiredStoreData)
            ->assertJson([
                'name' => $this->requiredStoreData['name'],
                'email' => $this->requiredStoreData['email'],
                'phone' => null,
                'score' => null
            ]);
    }

    public function test_store_lead_with_optional_fields_returns_correct_data(): void
    {
        $this->postJson('api/leads', $this->optionalStoreData)
            ->assertJson($this->optionalStoreData);
    }

    public function test_store_lead_calls_scoring_service(): void
    {
        $this->mock(LeadScoringServiceInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getLeadScore')->once();
        });

        $this->postJson('api/leads', $this->requiredStoreData);

        $lead = Lead::where('email', $this->requiredStoreData['email'])->first();

        $this->assertNotNull($lead->score);
    }
}