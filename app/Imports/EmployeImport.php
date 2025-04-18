<?php

namespace App\Imports;

use App\Models\Employe;
use App\Models\{Direction, Service, Fonction, Observation};
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EmployeImport implements ToModel, WithHeadingRow, WithValidation, WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        try {
            $direction = Direction::where('nom', $row['direction'])->firstOrFail();
            $fonction = Fonction::where('nom', $row['fonction'])->firstOrFail();
            $observation = Observation::where('observation', $row['observation'])->firstOrFail();

            $dateNaissance = $this->parseDate($row['date_de_naissance']);

            $id_service = null;
            if (!empty($row['service'])) {
                $service = Service::where('nom', $row['service'])->first();
                $id_service = $service ? $service->id : null;
            }

            $cin = $row['cin'];
            $telephone = $row['telephone'];

            if (DB::table('employe')->where('cin', $cin)->exists()) {
                return null;
            }

            if (DB::table('employe')->where('telephone', $telephone)->exists()) {
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

    private function parseDate($dateValue)
    {
        if (is_numeric($dateValue)) {
            return Carbon::instance(Date::excelToDateTimeObject((int)$dateValue));
        }
        return Carbon::createFromFormat('d/m/Y', $dateValue);
    }

    public function rules(): array
    {
        return [];
    }
}
