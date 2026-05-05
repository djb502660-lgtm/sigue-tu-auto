<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use App\Support\AdminEventLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserTrackingApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));

        $orders = ServiceOrder::query()
            ->with(['vehicle', 'status'])
            ->whereHas('client', function ($query) use ($request) {
                $query->where('email', $request->user()->email);
            })
            ->when($term !== '', function ($query) use ($term) {
                $query->where(function ($nested) use ($term) {
                    $nested->where('folio_number', 'like', "%{$term}%")
                        ->orWhereHas('vehicle', function ($vehicleQuery) use ($term) {
                            $vehicleQuery->where('plate', 'like', "%{$term}%");
                        });
                });
            })
            ->latest()
            ->paginate(15);

        AdminEventLogger::log(
            'user_queries',
            'api_tracking_query',
            'Consulta API de ordenes del usuario',
            $request->user(),
            [
                'term' => $term,
                'results' => $orders->total(),
            ]
        );

        return response()->json($orders);
    }

    public function show(Request $request, ServiceOrder $serviceOrder): JsonResponse
    {
        $serviceOrder->load(['client', 'vehicle', 'status', 'history.status']);

        if ($serviceOrder->client?->email !== $request->user()->email) {
            abort(403);
        }

        return response()->json($serviceOrder);
    }
}
