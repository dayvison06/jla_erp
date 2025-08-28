<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'employee_attachments';
    protected $fillable = [
        'employee_id',
        'name',
        'type',
        'path',
        'size',
        'uploaded_by',
    ];
}
