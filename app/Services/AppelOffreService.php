<?php

namespace App\Services;

use App\Models\AppelOffre;

class AppelOffreService {

    public function create(array $data) {
        return AppelOffre::create($data);
    }

    public function findAll($status) {
        $this->updateAppelExpire();

        return AppelOffre::where('status', $status)
            ->paginate(6);
    }

    public function getAppelOuvert() {
        $this->updateAppelExpire();

        return AppelOffre::where('status', 0)
            ->where('date_limite', '>=', now())
            ->paginate(6);
    }

    public function updateAppelExpire() {
        return AppelOffre::where('date_limite', '<', now())
            ->update(['status' => 1]);
    }

    public function updateAppelSoumis() {
        return AppelOffre::update(['status' => 2]);
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
