<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Departament extends Model
{
    protected $table = 'departments';

    protected $fillable = [
        'name',
        'description',
    ];

    public function employee() : BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_department', 'department_id', 'employee_id');
    }
}
