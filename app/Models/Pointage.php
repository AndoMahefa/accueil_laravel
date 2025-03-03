<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pointage extends Model
{
    use HasFactory;

    protected $table = "pointage";
    public $timestamps = false;
    protected $fillable = [
        'id',
        'date',
        'heure_arrivee',
        'heure_depart',
        'session',
        'id_employe',
        'id_statut'
    ];

    // Relation avec Employe
    public function employe()
    {
        return $this->belongsTo(Employe::class, 'id_employe');
    }

    // Relation avec Statut
    public function statut()
    {
        return $this->belongsTo(Statut::class, 'id_statut');
    }
}
