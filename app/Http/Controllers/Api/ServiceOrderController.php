<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ServiceOrder;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceOrderController extends Controller
{
    public function index()
    {
        $orders = ServiceOrder::with(['client', 'vehicle', 'status'])
            ->latest()
            ->paginate(15);

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client.name' => ['required', 'string', 'max:255'],
            'client.phone' => ['required', 'string', 'max:20'],
            'client.email' => ['nullable', 'email'],

            'vehicle.brand' => ['required', 'string', 'max:255'],
            'vehicle.model' => ['required', 'string', 'max:255'],
            'vehicle.color' => ['nullable', 'string', 'max:255'],
            'vehicle.plate' => ['required', 'string', 'max:50'],
            'vehicle.vin' => ['nullable', 'string', 'max:255'],
            'vehicle.mileage' => ['nullable', 'integer', 'min:0'],

            'entry_date' => ['nullable', 'date'],
            'work_description' => ['nullable', 'string'],
            'observations' => ['nullable', 'string'],
            'status_id' => ['nullable', Rule::exists('statuses', 'id')],
        ]);

        $client = Client::firstOrCreate(
            [
                'phone' => $validated['client']['phone'],
            ],
            [
                'name' => $validated['client']['name'],
                'email' => $validated['client']['email'] ?? null,
            ]
        );

        $vehicle = Vehicle::firstOrCreate(
            [
                'plate' => $validated['vehicle']['plate'],
            ],
            [
                'client_id' => $client->id,
                'brand' => $validated['vehicle']['brand'],
                'model' => $validated['vehicle']['model'],
                'color' => $validated['vehicle']['color'] ?? null,
                'vin' => $validated['vehicle']['vin'] ?? null,
                'mileage' => $validated['vehicle']['mileage'] ?? null,
            ]
        );

        $folioNumber = 'OS-' . now()->format('Ymd-His') . '-' . str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);

        $order = ServiceOrder::create([
            'folio_number' => $folioNumber,
            'client_id' => $client->id,
            'vehicle_id' => $vehicle->id,
            'status_id' => $validated['status_id'] ?? null,
            'entry_date' => $validated['entry_date'] ?? now(),
            'work_description' => $validated['work_description'] ?? null,
            'observations' => $validated['observations'] ?? null,
        ]);

        return response()->json(
            $order->load(['client', 'vehicle', 'status']),
            201
        );
    }

    public function show(ServiceOrder $serviceOrder)
    {
        $serviceOrder->load(['client', 'vehicle', 'status', 'history.status']);

        return response()->json($serviceOrder);
    }
}

