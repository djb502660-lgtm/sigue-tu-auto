<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use App\Models\Status;
use App\Models\StatusHistory;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index()
    {
        return response()->json(Status::orderBy('id')->get());
    }

    public function change(Request $request, ServiceOrder $serviceOrder)
    {
        $validated = $request->validate([
            'status_id' => ['required', 'exists:statuses,id'],
            'note' => ['nullable', 'string'],
        ]);

        $status = Status::findOrFail($validated['status_id']);

        $serviceOrder->update([
            'status_id' => $status->id,
        ]);

        StatusHistory::create([
            'service_order_id' => $serviceOrder->id,
            'status_id' => $status->id,
            'changed_by' => auth()->id(),
            'note' => $validated['note'] ?? null,
        ]);

        return response()->json(
            $serviceOrder->load(['status', 'history.status'])
        );
    }
}

