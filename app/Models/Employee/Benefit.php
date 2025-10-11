<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Benefit extends Model
{
    protected $table = 'benefits';

    protected $fillable = [
        'name',
        'description',
        'active'
    ];

   public function employees() : BelongsToMany
   {
       return $this->belongsToMany(Employee::class, 'employee_benefit', 'benefit_id', 'employee_id');
   }
}
