<?php

namespace App\Support;

use App\Models\AdminEvent;
use Illuminate\Contracts\Auth\Authenticatable;

class AdminEventLogger
{
    /**
     * @param  array<string, mixed>|null  $meta
     */
    public static function log(
        string $category,
        string $event,
        ?string $description = null,
        ?Authenticatable $actor = null,
        ?array $meta = null
    ): void {
        AdminEvent::create([
            'actor_id' => $actor?->getAuthIdentifier(),
            'category' => $category,
            'event' => $event,
            'description' => $description,
            'meta' => $meta,
        ]);
    }
}
