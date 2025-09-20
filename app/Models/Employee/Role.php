<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $table = 'employee_roles';

    protected $fillable = [
        'name',
        'base_salary',
        'description',
    ];

    public function employees() : belongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_has_roles', 'employee_id', 'role_id');
    }
}
