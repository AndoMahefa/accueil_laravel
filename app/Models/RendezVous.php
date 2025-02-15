<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    use HasFactory;

    protected $table = "rdv";
    protected $fillable = [
        'date_heure',
        'heure_fin',
        'id_service',
        'id_visiteur',
        'motif',
        'id_direction'
    ];

    public $timestamps = false;
    // Relations
    public function service()
    {
        return $this->belongsTo(Service::class, 'id_service');
    }

    public function visiteur()
    {
        return $this->belongsTo(Visiteur::class, 'id_visiteur');
    }

    public function direction() {
        return $this->belongsTo(Direction::class, 'id_direction');
    }
}
