<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employe extends Model {
    use HasFactory, SoftDeletes;

    public $timestamps = false;
    protected $table = "employe";
    protected $fillable = [
        'nom',
        'prenom',
        'date_de_naissance',
        'adresse',
        'cin',
        'telephone',
        'genre',
        'id_service',
        'id_direction',
        'id_fonction',
        'id_observation'
    ];

    protected $dates = ['deleted_at'];

    public function service() {
        return $this->belongsTo(Service::class, 'id_service');
    }

    public function roles() {
        return $this->belongsToMany(RoleService::class, 'role_employe', 'id_employe', 'id_role');
    }

    public function utilisateur() {
        return $this->hasOne(User::class, 'id_employe');
    }

    public function direction() {
        return $this->belongsTo(Direction::class, 'id_direction');
    }

    public function fonction() {
        return $this->belongsTo(Fonction::class, 'id_fonction');
    }

    public function observation() {
        return $this->belongsTo(Observation::class, 'id_observation');
    }
}
