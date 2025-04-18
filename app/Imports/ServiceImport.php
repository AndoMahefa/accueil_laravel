<?php

namespace App\Imports;

use App\Models\Service;
use App\Models\Direction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ServiceImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Trouver la direction correspondante
        $direction = Direction::where('nom', $row['direction'])->first();

        // Créer/mettre à jour le service
        return Service::updateOrCreate(
            ['nom' => $row['service']], // Colonne A
            ['id_direction' => $direction->id ?? null]
        );
    }
}
