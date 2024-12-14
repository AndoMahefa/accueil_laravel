<?php

namespace App\Services;

use App\Models\CreneauService;

class CreneauServiceManager {
    public function create(array $data) {
        return CreneauService::create($data);
    }

    public function findAllService($idService) {
        return CreneauService::where('id', '!=', $idService)
            ->orderBy('jour')
            ->orderBy('heure')
            ->get();
    }    

    public function findAll() {
        return CreneauService::all();
    }

    public function findById($id) {
        return CreneauService::findOrFail($id);
    }

    public function update($id, array $data) {
        $rdv = $this->findById($id);
        $rdv->update($data);
        return $rdv;
    }

    public function delete($idJour) {
        $creneau = Creneau::where('jour', $idJour)->get();
        foreach ($creneau as $c) {
            $c->delete();
        }
    }
}
