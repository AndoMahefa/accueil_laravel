<?php

namespace App\Imports;

use App\Models\Direction;
use App\Models\Fonction;
use App\Models\Service;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FonctionImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $direction = Direction::where('nom', $row['direction'])->first();
        $service = Service::where('nom', $row['service'])->first();

        return Fonction::updateOrCreate(
            ['nom' => $row['fonction']], // Colonne A
            [
                'id_direction' => $direction->id ?? null,
                'id_service' => $service->id ?? null
            ]
        );
    }
}
