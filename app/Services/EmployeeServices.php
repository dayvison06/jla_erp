<?php

namespace App\Services;

use App\Models\Employee\Benefit;
use App\Models\Employee\Employee;

class EmployeeServices
{

    public function processBenefits(Employee $employee, array $benefits): void
    {
      $benefitsFromDB = Benefit::all()->keyBy('name');
      $benefitsToEmployee = [];
      foreach ($benefits as $benefit) {
          if (isset($benefitsFromDB[$benefit])) {
              $benefitsToEmployee[] = $benefitsFromDB[$benefit]->id;
          }
      }
        $employee->benefits()->sync($benefitsToEmployee);
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
