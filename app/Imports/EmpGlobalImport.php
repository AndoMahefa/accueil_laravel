<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EmpGlobalImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Directions' => new DirectionImport(),
            'Services' => new ServiceImport(),
            'Fonctions' => new FonctionImport(),
            'Observations' => new ObservationImport(),
            'Employe' => new EmployeImport(),
        ];
    }
}
