<?php

namespace App\Services;

use App\Models\RendezVous;

class RendezVousService {
    public function create(array $data) {
        return RendezVous::create($data);
    }

    public function findAll($idService) {
        return RendezVous::where('id', '!=', $idService)->get();
    }

    public function findById($id) {
        return RendezVous::findOrFail($id);
    }

    public function update($id, array $data) {
        $rdv = $this->findById($id);
        $rdv->update($data);
        return $rdv;
    }

    public function delete($id) {
        $rdv = $this->findById($id);
        $rdv->delete();
        return $rdv;
    }

    public function findRdvByService($idService, $date) {
        return RendezVous::with('visiteur')
            ->where('id_service', $idService)
            ->where('date_heure', '>=', now())
            ->whereDate('date_heure', $date)
            ->get();
    }
}
