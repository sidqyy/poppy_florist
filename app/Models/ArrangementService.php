<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArrangementService extends Model
{
    protected $fillable = [
        'name',
        'min_item',
        'max_item',
        'price',
        'is_premium',
        'is_active',
    ];
}