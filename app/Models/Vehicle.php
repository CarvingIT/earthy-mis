<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'registration_number',
        'type',
        'brand',
        'model',
        'color',
        'purchased_on',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
