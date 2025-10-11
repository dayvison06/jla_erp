<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;

class Departament extends Model
{
    protected $table = 'departments';

    protected $fillable = [
        'name',
        'description',
    ];
}
