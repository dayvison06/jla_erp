<?php

namespace App;

class EmployeeServices
{

    public function processBenefits(array $benefits): array
    {
        // Example processing: filter out invalid benefits
        return array_filter($benefits, function ($benefit) {
            return isset($benefit['name']) && !empty($benefit['name']);
        });
    }

    public function processDependents(array $dependents): array
    {
        // Example processing: ensure each dependent has a valid relationship
        return array_filter($dependents, function ($dependent) {
            return isset($dependent['name'], $dependent['relationship']) && !empty($dependent['name']) && !empty($dependent['relationship']);
        });
    }
}
