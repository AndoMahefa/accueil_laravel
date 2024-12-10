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
        'id_service',
        'id_visiteur',
        'motif'
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
}
