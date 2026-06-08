<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Society extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'city',
        'joining_month',
        'flats_families',
        'chairman_name',
        'secretary_name',
        'contact_person_email',
        'phone',
        'rate_per_flat',
        'billing_amount',
        'vehicle_number',
    ];

    public function getRatePerFlatAttribute($value)
    {
        $billingAmount = (float) ($this->attributes['billing_amount'] ?? 0);
        if ($billingAmount > 0) {
            $flats = (float) preg_replace('/[^\d.]/', '', (string) $this->flats_families);
            if ($flats > 0) {
                return $billingAmount / $flats;
            }
        }
        return (float) ($value ?? 0);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
