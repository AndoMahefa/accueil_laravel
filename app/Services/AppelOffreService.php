<?php

namespace App\Services;

use App\Models\AppelOffre;

class AppelOffreService {

    public function create(array $data) {
        return AppelOffre::create($data);
    }

    public function findAll() {
        return AppelOffre::paginate(6);
    }

    public function findById($id) {
        return AppelOffre::findOrFail($id);
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
