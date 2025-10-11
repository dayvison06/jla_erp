<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'attachments';
    protected $fillable = [
        'employee_id',
        'name',
        'type',
        'path',
        'size',
        'uploaded_by',
    ];

    protected function getPathAttribute($value)
    {
        return asset('storage/' . $value);
    }
}
