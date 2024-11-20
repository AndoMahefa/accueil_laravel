<?php

namespace App\Services;

use App\Models\Service;

class ServiceManager {
    public function create(array $data) {
        return Service::create($data);
    }

    public function findAll($idService) {
        return Service::where('id', '!=', $idService)->get();
    }    

    public function findById($id) {
        return Service::findOrFail($id);
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
