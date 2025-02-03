<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Service extends Model{
    use HasFactory, SoftDeletes;

    protected $table = 'service';
    public $timestamps = false;
    protected $fillable = [
        'nom',
        'id_direction'
    ];

    protected $dates = ['deleted_at'];

    public function visiteurs() {
        return $this->belongsToMany(Visiteur::class, 'visiteur_service', 'id_service', 'id_visiteur')
            ->withPivot('motif_visite', 'statut', 'date_heure_arrivee');
    }

    public function direction() {
        return $this->belongsTo(Direction::class, 'id_direction');
    }

    public function tickets() {
        return $this->hasMany(Ticket::class, 'id_service');
    }

    public function employes() {
        return $this->hasMany(Employe::class, 'id_service');
    }

    public function roles() {
        return $this->hasMany(RoleService::class, 'id_service');
    }
}
