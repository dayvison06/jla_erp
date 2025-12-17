<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    protected $fillable = [
        'name',
        'client',
        'technical_manager',
        'start_date',
        'expected_end_date',
        'end_date',
        'status',
        'total_area',
        'type',
        'address',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_end_date' => 'date',
        'end_date' => 'date',
        'total_area' => 'decimal:2',
    ];
}
