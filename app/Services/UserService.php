<?php

namespace App\Services;

use App\Models\User;

class UserService {
    public function create(array $data) {
        return User::create($data);
    }

    public function findAll($idUser) {
        return User::where('id', $idUser)->get();
    }

    public function findById($id) {
        return User::findOrFail($id);
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
