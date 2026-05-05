<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use App\Support\AdminEventLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserTrackingController extends Controller
{
    public function index(Request $request): View
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
            ->paginate(15)
            ->withQueryString();

        AdminEventLogger::log(
            'user_queries',
            'web_tracking_query',
            'Consulta de ordenes desde modulo de usuario',
            $request->user(),
            [
                'term' => $term,
                'results' => $orders->total(),
            ]
        );

        return view('user.consulta', [
            'orders' => $orders,
            'term' => $term,
        ]);
    }
}
