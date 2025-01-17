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
        'id_service'
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
}
