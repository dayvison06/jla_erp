<?php

namespace App\Services;

use App\Models\Employee\Benefit;
use App\Models\Employee\Employee;

class EmployeeServices
{

    public function processBenefits(Employee $employee, array $benefits): void
    {
        debug('BENEFICIOS', $benefits);
        $employee->benefits()->sync($benefits);
    }

    public function processDependents(Employee $employee, array $dependents): void
    {
        foreach ($dependents as $dependent) {
            $employee->dependents()->updateOrCreate($dependent);
        }
    }

    public function processJobRoles(Employee $employee, array $jobRoles): void
    {
        $syncData = [];
        foreach ($jobRoles as $jobRole) {
            $roleId = $jobRole['role_id'];
            $syncData[$roleId] = [
                'start_date' => $jobRole['start_date'],
                'end_date' => $jobRole['end_date'] ?? null,
            ];
        }
        $employee->job_roles()->sync($syncData);
    }
}
