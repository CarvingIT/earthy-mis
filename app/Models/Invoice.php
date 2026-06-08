<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'society_id',
        'invoice_number',
        'billing_month',
        'total_amount',
        'status',
        'error_log',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function society()
    {
        return $this->belongsTo(Society::class);
    }
}
