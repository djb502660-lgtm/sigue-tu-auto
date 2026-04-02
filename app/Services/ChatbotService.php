<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ServiceOrder;
use App\Models\Vehicle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ChatbotService
{
    /**
     * Busca órdenes de servicio según el texto del usuario y un teléfono opcional.
     *
     * @return Collection<int, ServiceOrder>
     */
    public function findOrders(string $message, ?string $phoneHint): Collection
    {
        $text = trim($message);

        if ($folio = $this->extractFolio($text)) {
            $found = ServiceOrder::with(['client', 'vehicle', 'status'])
                ->where('folio_number', $folio)
                ->get();

            if ($found->isNotEmpty()) {
                return $found;
            }
        }

        $digits = $this->resolvePhoneDigits($text, $phoneHint);
        if ($digits !== '') {
            return $this->ordersByPhoneDigits($digits);
        }

        if ($plate = $this->extractPlate($text)) {
            return $this->ordersByPlate($plate);
        }

        return collect();
    }

    /**
     * Respuesta en texto cuando no hay API de IA o como respaldo.
     *
     * @param  Collection<int, ServiceOrder>  $orders
     */
    public function buildLocalReply(Collection $orders, string $userMessage): string
    {
        if ($orders->isEmpty()) {
            return 'No encontré una orden con esos datos. Puedes indicar: el folio (ej. OS-20260402-123456-789), '
                .'tu celular registrado en el taller, o escribir "placa" seguido del número de placas del vehículo.';
        }

        $lines = [];
        foreach ($orders as $order) {
            $v = $order->vehicle;
            $c = $order->client;
            $estado = $order->status?->name ?? 'sin registrar';
            $ingreso = $order->entry_date?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '—';

            $lines[] = sprintf(
                'Folio %s — Estado: %s. Vehículo: %s %s, placas %s. Cliente: %s. Ingreso: %s.',
                $order->folio_number,
                $estado,
                $v?->brand ?? '',
                $v?->model ?? '',
                $v?->plate ?? '',
                $c?->name ?? '',
                $ingreso
            );
        }

        return implode("\n\n", $lines);
    }

    /**
     * Intenta generar respuesta con OpenAI si hay clave configurada.
     *
     * @param  Collection<int, ServiceOrder>  $orders
     */
    public function maybeEnhanceWithAi(string $userMessage, Collection $orders, string $localFallback): string
    {
        $key = config('services.openai.key');
        if (! is_string($key) || $key === '') {
            return $localFallback;
        }

        $context = $orders->isEmpty()
            ? 'No hay órdenes coincidentes en la base de datos.'
            : $orders->map(fn (ServiceOrder $o) => $o->loadMissing(['client', 'vehicle', 'status'])->toArray())->values()->all();

        $system = 'Eres el asistente virtual del taller de mecánica del Instituto Superior Tecnológico Alberto Enríquez (ISTAE). '
            .'Responde en español, de forma breve y cordial. Solo informas sobre el estado del mantenimiento según los datos proporcionados. '
            .'Si no hay datos de orden, indica cómo consultar (folio, celular o placas). No inventes información que no esté en el contexto. '
            .'Contexto JSON de órdenes: '.json_encode($context, JSON_UNESCAPED_UNICODE);

        try {
            $response = Http::withToken($key)
                ->timeout(45)
                ->acceptJson()
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4o-mini'),
                    'messages' => [
                        ['role' => 'system', 'content' => $system],
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                    'max_tokens' => 500,
                    'temperature' => 0.4,
                ]);

            if (! $response->successful()) {
                return $localFallback;
            }

            $text = data_get($response->json(), 'choices.0.message.content');
            if (! is_string($text) || trim($text) === '') {
                return $localFallback;
            }

            return trim($text);
        } catch (\Throwable) {
            return $localFallback;
        }
    }

    private function extractFolio(string $text): ?string
    {
        if (preg_match('/OS-\d{8}-\d{6}-\d{3}/i', $text, $m)) {
            return $m[0];
        }

        return null;
    }

    private function extractPlate(string $text): ?string
    {
        if (preg_match('/(?:placa|placas|matr[íi]cula)[\s:]*([A-Z0-9\-]{5,12})/iu', $text, $m)) {
            return strtoupper(str_replace('-', '', $m[1]));
        }

        if (preg_match('/\b([A-Z]{2,3}[\s\-]?[0-9]{3,4})\b/u', $text, $m)) {
            return strtoupper(str_replace([' ', '-'], '', $m[1]));
        }

        return null;
    }

    private function resolvePhoneDigits(string $text, ?string $phoneHint): string
    {
        if ($phoneHint !== null && $phoneHint !== '') {
            $d = preg_replace('/\D+/', '', $phoneHint);

            return $this->lastDigitsForMatch($d);
        }

        if (preg_match_all('/\d{7,15}/', $text, $nums) && $nums[0] !== []) {
            return $this->lastDigitsForMatch($nums[0][array_key_last($nums[0])]);
        }

        return '';
    }

    private function lastDigitsForMatch(string $digits): string
    {
        $digits = preg_replace('/\D+/', '', $digits) ?? '';

        if (strlen($digits) >= 9) {
            return substr($digits, -9);
        }

        if (strlen($digits) >= 7) {
            return $digits;
        }

        return '';
    }

    /**
     * @return Collection<int, ServiceOrder>
     */
    private function ordersByPhoneDigits(string $matchDigits): Collection
    {
        $suffix = strlen($matchDigits) >= 9 ? substr($matchDigits, -9) : $matchDigits;

        $clientIds = Client::query()
            ->where(function ($q) use ($suffix) {
                $q->whereRaw("REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '+', '') LIKE ?", ['%'.$suffix]);
            })
            ->pluck('id');

        if ($clientIds->isEmpty()) {
            return collect();
        }

        return ServiceOrder::with(['client', 'vehicle', 'status'])
            ->whereIn('client_id', $clientIds)
            ->latest()
            ->take(5)
            ->get();
    }

    /**
     * @return Collection<int, ServiceOrder>
     */
    private function ordersByPlate(string $normalizedPlate): Collection
    {
        $vehicle = Vehicle::query()
            ->whereRaw(
                "REPLACE(UPPER(plate), '-', '') = ?",
                [strtoupper(str_replace('-', '', $normalizedPlate))]
            )
            ->first();

        if (! $vehicle) {
            return collect();
        }

        return ServiceOrder::with(['client', 'vehicle', 'status'])
            ->where('vehicle_id', $vehicle->id)
            ->latest()
            ->take(5)
            ->get();
    }
}
