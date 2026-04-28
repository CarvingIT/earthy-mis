<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplyItem extends Model
{

     protected $table='supplyitems';
     protected $fillable = [
        'Date',
        'quantity',
        'consumable_id',
        'description',
    ];

    public function consumable(){
        return $this->belongsTo(Consumable::class, 'consumable_id');
    }
}
