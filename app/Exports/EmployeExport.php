<?php

namespace App\Exports;

use App\Models\Employe;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeExport implements FromCollection, WithHeadings, WithColumnWidths, WithMapping
{
    protected $directionId;
    protected $serviceId;

    /**
     * Constructeur pour accepter des paramètres
     */
    public function __construct($directionId = null, $serviceId = null)
    {
        $this->directionId = $directionId;
        $this->serviceId = $serviceId;
    }

    /**
     * Récupère les données à exporter
     */
    public function collection()
    {
        $query = Employe::with(['direction', 'service', 'fonction', 'observation']);

        // Appliquer les filtres si des paramètres sont fournis
        if ($this->directionId) {
            $query->where('id_direction', $this->directionId);
        }
        if ($this->serviceId) {
            $query->where('id_service', $this->serviceId);
        }

        return $query->get();
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 40,
            'C' => 20,
            'D' => 30,
            'E' => 20,
            'F' => 20,
            'G' => 10,
            'H' => 30,
            'I' => 30,
            'J' => 30,
            'K' => 30
        ];
    }

    /**
     * Définit les en-têtes de colonnes
     */
    public function headings(): array
    {
        return [
            'Nom',
            'Prénom',
            'Date de naissance',
            'Adresse',
            'CIN',
            'Téléphone',
            'Genre',
            'Direction',
            'Service',
            'Fonction',
            'Observation',
        ];
    }

    /**
     * Mappe les données de chaque employé
     */
    public function map($employe): array
    {
        return [
            $employe->nom,
            $employe->prenom,
            optional($employe->date_de_naissance)->format('d/m/Y') ?? '',
            $employe->adresse,
            $employe->cin,
            $employe->telephone,
            $employe->genre,
            $employe->direction->nom ?? '', // Direction (nullable)
            $employe->service->nom ?? '', // Service (nullable)
            $employe->fonction->nom ?? '', // Fonction
            $employe->observation->observation ?? '', // Observation
        ];
    }
}
