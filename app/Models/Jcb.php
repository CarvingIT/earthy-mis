<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jcb extends Model
{
    protected $fillable = [
        'Date',
        'duration',
        'description',
    ];
}
