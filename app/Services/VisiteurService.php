<?php

namespace App\Services;

use App\Models\Visiteur;

class VisiteurService {

    public function create(array $data) {
        return Visiteur::create($data);
    }

    public function findAll() {
        return Visiteur::paginate(6);
    }

    public function findById($id) {
        return Visiteur::findOrFail($id);
    }

    public function update($id, array $data) {
        $service = $this->findById($id);
        $service->update($data);
        return $service;
    }

    public function delete($id) {
        $service = $this->findById($id);
        $service->delete();
        
        return $service;
    }
}
