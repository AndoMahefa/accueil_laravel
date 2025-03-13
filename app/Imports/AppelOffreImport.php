<?php

namespace App\Imports;

use App\Models\AppelOffreChamps;
use App\Models\AppelOffreDonnees;
use App\Models\AppelOffreTable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AppelOffreImport implements ToCollection, WithHeadingRow
{
    protected $id_reference;

    public function __construct($id_reference)
    {
        $this->id_reference = $id_reference;
    }

    public function collection(Collection $rows) {
        // Récupérer tous les champs une seule fois
        $champs = AppelOffreChamps::pluck('id', 'nom_champ');

        foreach ($rows as $row) {
            // Trouver l'objet
            $nomObjet = $champs->has('Objet')
                ? $row[strtolower('Objet')] ?? "Appel d'offre " . uniqid()
                : "Appel d'offre " . uniqid();

            $appelOffre = AppelOffreTable::create([
                'appel_offre' => $nomObjet,
                'id_reference' => $this->id_reference
            ]);

            // Mapping des données
            foreach ($champs as $nomChamp => $idChamp) {
                if (isset($row[strtolower($nomChamp)])) {
                    AppelOffreDonnees::create([
                        'id_appel_offre_champs' => $idChamp,
                        'valeur' => $row[strtolower($nomChamp)],
                        'id_appel_offre' => $appelOffre->id
                    ]);
                }
            }
        }
    }
}