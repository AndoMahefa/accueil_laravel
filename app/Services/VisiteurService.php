<?php

namespace App\Services;

use App\Models\Visiteur;
use Illuminate\Http\Request;

class VisiteurService {

    public function create(array $data) {
        return Visiteur::create($data);
    }

    public function findAll(?string $search = null) {
        return Visiteur::when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nom', 'like', "%$search%")
                      ->orWhere('prenom', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%");
                });
            })
            ->paginate(10);
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
