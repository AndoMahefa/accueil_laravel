<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\CreneauService;

class CreneauServiceManager
{
    public function create(array $data)
    {
        return CreneauService::create($data);
    }

    public function findAllService($idService)
    {
        return CreneauService::where('id_service', $idService)
            ->orderBy('jour')
            ->orderBy('heure')
            ->get();
    }

    public function findAll()
    {
        return CreneauService::all();
    }

    public function findById($id)
    {
        return CreneauService::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $rdv = $this->findById($id);
        $rdv->update($data);
        return $rdv;
    }

    public function delete($idService, $idJour, $periodes)
    {
        // Crée une requête pour cibler les créneaux du jour donné
        $query = CreneauService::where('id_service', $idService)
            ->where('jour', $idJour);

        if (empty($periodes)) {
            return response()->json(['message' => 'Aucune période sélectionnée'], 400);
        }

        // Applique les filtres en fonction des périodes sélectionnées
        $query->where(function ($subQuery) use ($periodes) {
            if (in_array('matin', $periodes)) {
                $subQuery->orWhere('heure', '<=', '12:00:00');
            }
            if (in_array('apres-midi', $periodes)) {
                $subQuery->orWhere('heure', '>', '12:00:00');
            }
        });

        // Supprime les créneaux correspondant aux conditions
        $query->delete();
        Log::info($query->toRawSql());

        return response()->json(['message' => 'Créneaux supprimés avec succès'], 200);
    }

    public function deleteByDirection($idDirection, $idJour, $periodes)
    {
        // Crée une requête pour cibler les créneaux du jour donné
        $query = CreneauService::where('id_direction', $idDirection)
            ->where('jour', $idJour);

        if (empty($periodes)) {
            return response()->json(['message' => 'Aucune période sélectionnée'], 400);
        }

        // Applique les filtres en fonction des périodes sélectionnées
        $query->where(function ($subQuery) use ($periodes) {
            if (in_array('matin', $periodes)) {
                $subQuery->orWhere('heure', '<=', '12:00:00');
            }
            if (in_array('apres-midi', $periodes)) {
                $subQuery->orWhere('heure', '>', '12:00:00');
            }
        });

        // Supprime les créneaux correspondant aux conditions
        $query->delete();
        Log::info($query->toRawSql());

        return response()->json(['message' => 'Créneaux supprimés avec succès'], 200);
    }

    public function jourDispoService($idService)
    {
        return CreneauService::select('jour')
            ->distinct()
            ->where('id_service', $idService)
            ->get();
    }

    public function jourDispoDirection($idDirection)
    {
        return CreneauService::select('jour')
            ->distinct()
            ->where('id_direction', $idDirection)
            ->whereNull('id_service')
            ->get();
    }

    public function getCreneauxServiceJour($idService, $dayOfWeek)
    {
        return CreneauService::select('id', 'heure', 'heure_fin', 'jour')
            ->where('id_service', $idService)
            ->where('jour', $dayOfWeek)
            ->get();
    }

    public function getCreneauxDirectionJour($idDirection, $dayOfWeek)
    {
        return CreneauService::select('id', 'heure', 'heure_fin', 'jour')
            ->where('id_direction', $idDirection)
            ->whereNull('id_service')
            ->where('jour', $dayOfWeek)
            ->get();
    }
}
