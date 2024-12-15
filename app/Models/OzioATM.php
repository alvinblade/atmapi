<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class OzioATM extends Model
{
    protected $guarded = [];

    protected $table = "ozio_atms";

    protected $casts = [
        'total_amount' => 'float'
    ];
}
