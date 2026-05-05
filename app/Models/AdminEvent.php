<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminEvent extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'actor_id',
        'category',
        'event',
        'description',
        'meta',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
