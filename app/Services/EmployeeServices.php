<?php

namespace App\Services;

use App\Models\Employee\Employee;

class EmployeeServices
{

    public function processBenefits(array $benefits): array
    {
        // Example processing: filter out invalid benefits
        return array_filter($benefits, function ($benefit) {
            return isset($benefit['name']) && !empty($benefit['name']);
        });
    }

    public function processDependents(Employee $employee, array $dependents): void
    {

        foreach ($dependents as $dependent) {
            $purposes = $dependent['purposes'] ?? [];
            unset($dependent['purposes']);

            foreach ($purposes as $purpose) {
                switch ($purpose) {
                    case 'income_tax':
                        $dependent['is_income_tax_dependent'] = true;
                        break;
                    case 'health_plan':
                        $dependent['is_health_plan_dependent'] = true;
                        break;
                }
            }

            $employee->dependents()->updateOrCreate($dependent);
        }
    }

    public function cleanCpf(string $cpf): string
    {
        return preg_replace("/\D/", '', $cpf);
    }
}
