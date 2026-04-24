<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Weight extends Model
{
    protected $fillable = [
        'Date',
        'vehicle_id',
        'gross_weight',
        'tare_weight',
        'net_weight',
        'number_of_buckets',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
 
// Class ends
}
