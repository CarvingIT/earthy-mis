<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Windrow extends Model
{
    protected $fillable = [
        'windrow_number',
        'start_date',
        'end_date',
        'weight_in',
        'out_date',
        'screening_date', 
    ]; 
}
