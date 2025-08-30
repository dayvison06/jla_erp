<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
        'cnpj',
        'rg',
        'issuing_agency',
        'issue_date',
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
        'pix_key',
        'role',
        'department',
        'contract_type',
        'admission_date',
        'termination_date',
        'salary',
        'work_schedule',
        'last_exam_date',
        'next_exam_date',
        'aso_result',
        'allergies',
        'blood_type',
        'accident_history',
        'escolarity',
        'courses',
        'certifications',
        'experience',
        'benefits',
        'role_history',
        'dependents',
        'attachments',
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

    public function dependents()
    {
        return $this->hasMany(Dependent::class);
    }

    public function attachments() : HasMany
    {
        return $this->hasMany(Attachment::class);
    }
}
