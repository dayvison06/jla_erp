<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Depedent extends Model
{
    protected $table = 'employee_dependents';

    protected $fillable = [
        'employee_id',
        'name',
        'relationship',
        'birth_date',
        'cpf',
        'rg',
        'issuing_agency',
        'issue_date',
        'is_income_tax_dependent',
        'is_health_plan_dependent',
        'civil_state',
    ];

    public function employee() : BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
