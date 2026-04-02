<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChatbotService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(
        private ChatbotService $chatbot
    ) {}

    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $message = $validated['message'];
        $phone = $validated['phone'] ?? null;

        $orders = $this->chatbot->findOrders($message, $phone);
        $local = $this->chatbot->buildLocalReply($orders, $message);
        $reply = $this->chatbot->maybeEnhanceWithAi($message, $orders, $local);

        return response()->json([
            'reply' => $reply,
            'orders_found' => $orders->count(),
        ]);
    }
}
