<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trips extends Model
{
    //
    protected $fillable=[
        'Date',
        'vehicle_id',
        'purpose',
        'km',
        ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }


}
