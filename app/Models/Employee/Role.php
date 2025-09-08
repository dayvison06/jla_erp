<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $table = 'employee_roles';

    protected $fillable = [
        'name',
        'base_salary',
        'description',
    ];

    public function employees() : hasMany
    {
        return $this->hasMany(Employee::class, 'id', 'employee_id');
    }
}
