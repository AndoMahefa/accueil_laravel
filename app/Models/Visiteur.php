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
        "telephone"
    ];

    // public function services() {
    //     return $this->belongsToMany(Service::class, 'visiteur_service', 'id_visiteur', 'id_service')
    //     ->withPivot(['motif_visite', 'statut', 'date_heure_arrivee', 'id_direction', 'id_fonction'])
    //     ->withTimestamps(false);
    // }

    public function directions() {
        return $this->belongsToMany(Direction::class, 'visiteur_service', 'id_visiteur', 'id_direction')
        ->withPivot(['motif_visite', 'statut', 'date_heure_arrivee', 'id_service', 'id_fonction']);
    }

    public function tickets() {
        return $this->hasMany(Ticket::class, 'id_visiteur');
    }
}
