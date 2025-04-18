<?php

namespace App\Imports;

use App\Models\Direction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DirectionImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return Direction::firstOrCreate([
            'nom' => $row['direction']
        ]);
    }
}
