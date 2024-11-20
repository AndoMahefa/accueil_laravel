<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visiteur extends Model {
    use HasFactory;

    protected $table = 'visiteur';
    public $timestamps = false;
    protected $fillable = [
        "nom",
        "prenom",
        "cin",
        "email",
        "telephone",
        "date_heure_arrivee"
    ];

    public function services() {
        return $this->belongsToMany(Service::class, 'visiteur_service', 'id_visiteur', 'id_service')
        ->withPivot('motif_visite', 'statut','date_heure_arrivee');
    }
}