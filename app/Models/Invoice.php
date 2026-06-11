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
        'mail_sent_count',
        'dispatch_history',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'dispatch_history' => 'array',
    ];

    public function society()
    {
        return $this->belongsTo(Society::class);
    }
}
