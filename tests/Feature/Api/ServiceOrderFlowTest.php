<?php

namespace Tests\Feature\Api;

use App\Models\Client;
use App\Models\ServiceOrder;
use App\Models\Status;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_service_order(): void
    {
        $this->seed(StatusSeeder::class);

        $response = $this->postJson('/api/service-orders', $this->validPayload());

        $response->assertUnauthorized();
    }

    public function test_authenticated_maintenance_user_can_create_service_order_with_related_models(): void
    {
        $this->seed(StatusSeeder::class);

        $user = User::factory()->maintenance()->create();

        $response = $this->actingAs($user)->postJson('/api/service-orders', $this->validPayload());

        $response->assertCreated()
            ->assertJsonPath('client.phone', '5551112222')
            ->assertJsonPath('vehicle.plate', 'ABC-123');

        $this->assertDatabaseCount('clients', 1);
        $this->assertDatabaseCount('vehicles', 1);
        $this->assertDatabaseCount('service_orders', 1);
    }

    public function test_guest_cannot_change_service_order_status(): void
    {
        $this->seed(StatusSeeder::class);

        $order = $this->createOrder();
        $targetStatus = Status::where('slug', 'en-reparacion')->firstOrFail();

        $response = $this->postJson("/api/service-orders/{$order->id}/status", [
            'status_id' => $targetStatus->id,
            'note' => 'Cambio de prueba',
        ]);

        $response->assertUnauthorized();
    }

    public function test_authenticated_maintenance_user_can_change_status_and_create_history_record(): void
    {
        $this->seed(StatusSeeder::class);

        $user = User::factory()->maintenance()->create();
        $order = $this->createOrder();
        $targetStatus = Status::where('slug', 'en-reparacion')->firstOrFail();

        $response = $this->actingAs($user)->postJson("/api/service-orders/{$order->id}/status", [
            'status_id' => $targetStatus->id,
            'note' => 'Cambio de prueba',
        ]);

        $response->assertOk()
            ->assertJsonPath('status.id', $targetStatus->id);

        $this->assertDatabaseHas('service_orders', [
            'id' => $order->id,
            'status_id' => $targetStatus->id,
        ]);

        $this->assertDatabaseHas('status_histories', [
            'service_order_id' => $order->id,
            'status_id' => $targetStatus->id,
            'changed_by' => $user->id,
            'note' => 'Cambio de prueba',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(): array
    {
        return [
            'client' => [
                'name' => 'Cliente Prueba',
                'phone' => '5551112222',
                'email' => 'cliente@example.com',
            ],
            'vehicle' => [
                'brand' => 'Nissan',
                'model' => 'Versa',
                'color' => 'Azul',
                'plate' => 'ABC-123',
                'vin' => '1N4AL3AP9JC123456',
                'mileage' => 12000,
            ],
            'work_description' => 'Falla en frenos',
            'observations' => 'Ruido al frenar',
        ];
    }

    private function createOrder(): ServiceOrder
    {
        $client = Client::create([
            'name' => 'Cliente Base',
            'phone' => '5550000000',
            'email' => 'base@example.com',
        ]);

        $vehicle = Vehicle::create([
            'client_id' => $client->id,
            'brand' => 'Mazda',
            'model' => '3',
            'plate' => 'XYZ-987',
        ]);

        $status = Status::where('slug', 'recibido')->firstOrFail();

        return ServiceOrder::create([
            'folio_number' => 'OS-TEST-001',
            'client_id' => $client->id,
            'vehicle_id' => $vehicle->id,
            'status_id' => $status->id,
            'entry_date' => now(),
        ]);
    }
}
