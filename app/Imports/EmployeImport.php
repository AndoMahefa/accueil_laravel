<?php

namespace App\Imports;

use App\Models\Employe;
use App\Models\{Direction, Service, Fonction, Observation};
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EmployeImport implements ToModel, WithHeadingRow, WithValidation, WithStartRow
{
    // Définir la ligne de début (après les en-têtes)
    public function startRow(): int
    {
        return 2; // Commencer à la deuxième ligne
    }

    // Transformation des en-têtes
    public function headingRow(): int {
        return 1; // La première ligne contient les en-têtes
    }

    public function model(array $row) {
        try {
            // Convertir les noms/codes en IDs
            $direction = Direction::where('nom', $row['direction'])->first();
            $fonction = Fonction::where('nom', $row['fonction'])->first();
            $observation = Observation::where('observation', $row['observation'])->first();

            // Vérification des références
            if (!$direction || !$fonction || !$observation) {
                $missing = [];
                if (!$direction) $missing[] = 'Direction: ' . $row['direction'];
                if (!$fonction) $missing[] = 'Fonction: ' . $row['fonction'];
                if (!$observation) $missing[] = 'Observation: ' . $row['observation'];

                return null;
            }

            // Gestion de la date de naissance
            try {
                $dateValue = $row['date_de_naissance'];
                $dateNaissance = null;

                // Vérifier si c'est un nombre entier (date Excel)
                if (is_numeric($dateValue)) {
                    $dateNaissance = Carbon::instance(Date::excelToDateTimeObject((int)$dateValue));
                }
                // Essayer de parser comme date au format jour/mois/année
                else {
                    $dateNaissance = Carbon::createFromFormat('d/m/Y', $dateValue);
                }

                Log::info("Date de naissance convertie: " . $dateNaissance->format('Y-m-d'));

            } catch (\Exception $e) {
                return null;
            }

            // Recherche conditionnelle du service
            $id_service = null;
            if (!empty($row['service'])) {
                $service = Service::where('nom', $row['service'])->first();
                $id_service = $service ? $service->id : null;
            }

            // Nettoyage des valeurs CIN et téléphone (suppression des espaces)
            // $cin = str_replace(' ', '', $row['cin']);
            // $telephone = str_replace(' ', '', $row['telephone']);

            $cin = $row['cin'];
            $telephone = $row['telephone'];

            // Vérifier si le CIN existe déjà
            $cinExists = DB::table('employe')->where('cin', str_replace(' ', '', $row['cin']))->exists();
            if ($cinExists) {
                return null;
            }

            // Vérifier si le téléphone existe déjà
            $telExists = DB::table('employe')->where('telephone', str_replace(' ', '', $row['telephone']))->exists();
            if ($telExists) {
                return null;
            }

            return new Employe([
                'nom' => $row['nom'],
                'prenom' => $row['prenom'],
                'date_de_naissance' => $dateNaissance,
                'adresse' => $row['adresse'],
                'cin' => $cin,
                'telephone' => $telephone,
                'genre' => $row['genre'],
                'id_direction' => $direction->id,
                'id_service' => $id_service,
                'id_fonction' => $fonction->id,
                'id_observation' => $observation->id,
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function rules(): array {
        // Règles de validation minimales
        return [];
    }
}