<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Turning extends Model
{
        protected $fillable = [
        'windrow_id',
        'Date',
        'duration',
    ];

    public function windrow(): BelongsTo
    {
        return $this->belongsTo(Windrow::class, 'windrow_id');
    }

}
