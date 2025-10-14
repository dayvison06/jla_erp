<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'birth_date',
        'gender',
        'civil_state',
        'nationality',
        'birthplace',
        'cpf',
        'rg',
        'cnpj',
        'issuing_agency',
        'issue_date',
        'education_level',
        'voter_registration',
        'military_certificate',
        'mother_name',
        'father_name',
        'photo',
        'status',
        'ctps_number',
        'ctps_series',
        'ctps_state',
        'pis_pasep',
        'nit',
        'cnh',
        'cnh_category',
        'cnh_expiry',
        'professional_registration',
        'postal_code',
        'street',
        'number',
        'complement',
        'district',
        'city',
        'state',
        'phone',
        'mobile',
        'email',
        'emergency_contact',
        'emergency_phone',
        'bank',
        'agency',
        'account',
        'account_type',
        'pix_key_type',
        'pix_key',
        'contract_type',
        'admission_date',
        'termination_date',
        'salary',
        'last_exam_date',
        'next_exam_date',
        'aso_result',
        'allergies',
        'blood_type',
        'accident_history',
        'additional_info',
        'deleted_at',
    ];

    protected $casts = [
        'cpf' => 'string',
    ];

    public function getCpfAttribute($value) : string
    {
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $value);
    }

    public function setCpfAttribute($value) : void
    {
        $this->attributes['cpf'] = preg_replace("/\D/", '', $value);
    }

    public function dependents() : HasMany
    {
        return $this->hasMany(Depedent::class, 'employee_id', 'id');
    }

    public function benefits() : BelongsToMany
    {
        return $this->belongsToMany(Benefit::class, 'employee_benefit', 'employee_id', 'benefit_id');
    }

    public function attachments() : HasMany
    {
        return $this->hasMany(Attachment::class, 'employee_id', 'id');
    }

    public function job_roles() : BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'employee_job_role', 'employee_id', 'role_id')
            ->withPivot('start_date', 'end_date')->withTimestamps();
    }

    public function department() : BelongsToMany
    {
        return $this->belongsToMany(Departament::class, 'employee_department', 'employee_id', 'department_id');
    }
}
