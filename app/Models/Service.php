<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Service extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'service';
    public $timestamps = false;
    protected $fillable = [
        'nom',
        'email',
        'mot_de_passe',
        'telephone'
    ];

    public function getAuthPassword() {
        return $this->mot_de_passe;
    }

    protected $hidden = [
        'mot_de_passe'
    ];

    public function visiteurs() {
        return $this->belongsToMany(Visiteur::class, 'visiteur_service', 'id_service', 'id_visiteur')
            ->withPivot('motif_visite', 'statut', 'date_heure_arrivee');
    }
}