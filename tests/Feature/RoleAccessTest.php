<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ServiceOrder;
use App\Models\Status;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_role_cannot_access_operational_module(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);

        $response = $this->actingAs($user)->get('/sistema');

        $response->assertRedirect(route('consulta'));
    }

    public function test_maintenance_role_can_access_operational_module(): void
    {
        $user = User::factory()->maintenance()->create();

        $response = $this->actingAs($user)->get('/sistema');

        $response->assertOk();
    }

    public function test_admin_role_can_access_user_management_module(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertOk();
    }

    public function test_admin_role_cannot_access_operational_module(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/sistema');

        $response->assertRedirect(route('admin.monitor.dashboard'));
    }

    public function test_non_admin_cannot_access_user_management_module(): void
    {
        $user = User::factory()->maintenance()->create();

        $response = $this->actingAs($user)->get('/admin/users');

        $response->assertRedirect(route('sistema'));
    }

    public function test_user_role_can_only_view_own_orders_in_user_module(): void
    {
        $this->seed(StatusSeeder::class);

        $user = User::factory()->create([
            'email' => 'cliente1@example.com',
            'role' => User::ROLE_USER,
        ]);

        $ownOrder = $this->createOrderForClientEmail('cliente1@example.com', 'OS-OWN-001', 'AAA-111');
        $this->createOrderForClientEmail('cliente2@example.com', 'OS-OTHER-001', 'BBB-222');

        $response = $this->actingAs($user)->get('/consulta');

        $response->assertOk();
        $response->assertSee($ownOrder->folio_number);
        $response->assertDontSee('OS-OTHER-001');
    }

    public function test_user_role_cannot_call_operational_api_endpoints(): void
    {
        $this->seed(StatusSeeder::class);

        $user = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);

        $response = $this->actingAs($user)->getJson('/api/service-orders');

        $response->assertForbidden();
    }

    public function test_user_role_can_call_personal_tracking_api_endpoint(): void
    {
        $this->seed(StatusSeeder::class);

        $user = User::factory()->create([
            'email' => 'cliente1@example.com',
            'role' => User::ROLE_USER,
        ]);

        $this->createOrderForClientEmail('cliente1@example.com', 'OS-OWN-002', 'CCC-333');
        $this->createOrderForClientEmail('cliente2@example.com', 'OS-OTHER-002', 'DDD-444');

        $response = $this->actingAs($user)->getJson('/api/my-service-orders');

        $response->assertOk()
            ->assertJsonFragment(['folio_number' => 'OS-OWN-002'])
            ->assertJsonMissing(['folio_number' => 'OS-OTHER-002']);
    }

    public function test_admin_role_cannot_call_operational_api_endpoints(): void
    {
        $this->seed(StatusSeeder::class);

        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->getJson('/api/service-orders');

        $response->assertForbidden();
    }

    private function createOrderForClientEmail(string $email, string $folio, string $plate): ServiceOrder
    {
        $client = Client::create([
            'name' => "Cliente {$folio}",
            'phone' => fake()->numerify('099#######'),
            'email' => $email,
        ]);

        $vehicle = Vehicle::create([
            'client_id' => $client->id,
            'brand' => 'Mazda',
            'model' => '3',
            'plate' => $plate,
        ]);

        $status = Status::where('slug', 'recibido')->firstOrFail();

        return ServiceOrder::create([
            'folio_number' => $folio,
            'client_id' => $client->id,
            'vehicle_id' => $vehicle->id,
            'status_id' => $status->id,
            'entry_date' => now(),
        ]);
    }
}
