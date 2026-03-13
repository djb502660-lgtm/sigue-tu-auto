<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    public function serviceOrders()
    {
        return $this->hasMany(ServiceOrder::class);
    }

    public function history()
    {
        return $this->hasMany(StatusHistory::class);
    }
}

