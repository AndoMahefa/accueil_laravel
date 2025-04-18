<?php

namespace App\Imports;

use App\Models\Observation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ObservationImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return Observation::firstOrCreate(['observation' => $row['observation']]);
    }
}
