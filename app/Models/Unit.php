<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'related_unit_id',
        'related_unit_quantity',
    ];

    /**
     * Get the related unit (self-referential relationship).
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'related_unit_id');
    }
}
