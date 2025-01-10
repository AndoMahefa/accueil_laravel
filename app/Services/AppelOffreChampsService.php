<?php

namespace App\Services;

use App\Models\AppelOffreChamps;

class AppelOffreChampsService {

    public function create(array $data) {
        return AppelOffreChamps::create($data);
    }

    public function findall() {
        return AppelOffreChamps::all();
    }

    public function findById($id) {
        return AppelOffreChamps::findOrFail($id);
    }

    public function update($id, array $data) {
        $appel = $this->findById($id);
        $appel->update($data);
        return $appel;
    }

    public function delete($id) {
        $appel = $this->findById($id);
        $appel->delete();

        return $appel;
    }
}
