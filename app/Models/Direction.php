<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direction extends Model {
    use HasFactory;

    public $timestamps = false;
    protected $table = "direction";
    protected $fillable = [
        'nom',
        'id_parent_dir'
    ];

    protected $dates = ['deleted_at'];

    public function services() {
        return $this->hasMany(Service::class, 'id_direction');
    }

    public function visiteurs() {
        return $this->belongsToMany(Visiteur::class, 'visiteur_service', 'id_direction', 'id_visiteur')
            ->withPivot('motif_visite', 'statut', 'date_heure_arrivee', 'id_service', 'id_fonction');
    }
}
