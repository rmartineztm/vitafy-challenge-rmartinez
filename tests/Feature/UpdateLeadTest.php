<?php


use App\Contracts\LeadScoringServiceInterface;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class UpdateLeadTest extends TestCase
{
    private array $requiredUpdateData;
    private array $optionalUpdateData;

    private string $leadUuid;
    private Lead $lead;

    protected function setUp(): void
    {
        parent::setUp();

        $this->lead = Lead::factory()->create();

        $this->requiredUpdateData = [
            'name' => fake()->name,
            'email' => fake()->email
        ];

        $this->optionalUpdateData = [
            'name' => fake()->name,
            'email' => fake()->email,
            'phone' => fake()->phoneNumber
        ];

        $this->leadUuid = $this->lead->uuid;
    }

    public function test_update_lead_with_required_fields_returns_200_ok(): void
    {
        $this->putJson("api/leads/$this->leadUuid", $this->requiredUpdateData)->assertOk();
    }

    public function test_update_lead_with_optional_fields_returns_200_ok(): void
    {
        $this->putJson("api/leads/$this->leadUuid", $this->optionalUpdateData)->assertOk();
    }

    public function test_update_lead_field_name_is_not_string_returns_422_unprocessable(): void
    {
        $this->requiredUpdateData['name'] = 1111111111;

        $this->putJson("api/leads/$this->leadUuid", $this->requiredUpdateData)->assertUnprocessable();
    }

    public function test_update_lead_field_name_exceeds_max_255_returns_422_unprocessable(): void
    {
        $this->requiredUpdateData['name'] = Str::repeat('a', 256);

        $this->putJson("api/leads/$this->leadUuid", $this->requiredUpdateData)->assertUnprocessable();
    }

    public function test_update_lead_field_email_exceeds_max_255_returns_422_unprocessable(): void
    {
        $this->requiredUpdateData['email'] = Str::repeat('a', 100) . '@' . Str::repeat('a', 152) . '.com';

        $this->putJson("api/leads/$this->leadUuid", $this->requiredUpdateData)->assertUnprocessable();
    }

    public function test_update_lead_field_email_is_not_valid_email_returns_422_unprocessable(): void
    {
        $this->requiredUpdateData['email'] = fake()->text();

        $this->putJson("api/leads/$this->leadUuid", $this->requiredUpdateData)->assertUnprocessable();
    }

    public function test_update_lead_field_email_duplicate_returns_422_unprocessable(): void
    {
        $email = fake()->email;

        Lead::factory()->create(['email' => $email]);

        $this->requiredUpdateData['email'] = $email;

        $this->putJson("api/leads/$this->leadUuid", $this->requiredUpdateData)->assertUnprocessable();
    }

    public function test_update_lead_field_phone_exceeds_max_20_returns_422_unprocessable(): void
    {
        $this->optionalUpdateData['phone'] = Str::repeat('1', 21);

        $this->putJson("api/leads/$this->leadUuid", $this->optionalUpdateData)->assertUnprocessable();
    }

    public function test_update_lead_field_phone_is_not_string_returns_422_unprocessable(): void
    {
        $this->optionalUpdateData['phone'] = 1111111111;

        $this->putJson("api/leads/$this->leadUuid", $this->optionalUpdateData)->assertUnprocessable();
    }

    public function test_update_lead_with_empty_request_returns_422_unprocessable(): void
    {
        $this->putJson("api/leads/$this->leadUuid")
            ->assertUnprocessable();
    }

    public function test_update_lead_field_email_not_changed_returns_200_ok()
    {
        $this->putJson("api/leads/$this->leadUuid", [
            'name' => $this->requiredUpdateData['name'],
            'email' => $this->lead->email
        ])->assertOk();
    }

    public function test_update_lead_returns_lead_resource(): void
    {
        $this->putJson("api/leads/$this->leadUuid", $this->requiredUpdateData)
            ->assertExactJson(LeadResource::make($this->lead->refresh())->response()->getData(true));
    }

    public function test_update_lead_returns_correct_data(): void
    {
        $this->putJson("api/leads/$this->leadUuid", $this->requiredUpdateData)
            ->assertJson([
                'name' => $this->requiredUpdateData['name'],
                'email' => $this->requiredUpdateData['email'],
                'phone' => null,
                'score' => null
            ]);
    }

    public function test_update_lead_with_optional_fields_returns_correct_data(): void
    {
        $this->putJson("api/leads/$this->leadUuid", $this->optionalUpdateData)
            ->assertJson($this->optionalUpdateData);
    }

    public function test_update_lead_calls_scoring_service(): void
    {
        $this->mock(LeadScoringServiceInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getLeadScore')->once();
        });

        $this->putJson("api/leads/$this->leadUuid", $this->requiredUpdateData);

        $this->assertNotNull($this->lead->refresh()->score);
    }
}