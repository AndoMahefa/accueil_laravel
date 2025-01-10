<?php

namespace App\Services;

use App\Models\Employe;

class EmployeService {
    public function create(array $data) {
        return Employe::create($data);
    }

    public function findAll($idService) {
        return Employe::where('id_service', $idService)->get();
    }

    public function findById($id) {
        return Employe::findOrFail($id);
    }

    public function update($id, array $data) {
        $emp = $this->findById($id);
        $emp->update($data);
        return $emp;
    }

    public function delete($id) {
        $emp = $this->findById($id);
        $emp->delete();
    }
}
