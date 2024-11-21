<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'ticket';
    protected $fillable = [
        'temps_estime',
        'id_service',
        'id_visiteur',
        'date',
        'heure_prevu'
    ];

    public $timestamps = false;

    public function visiteur()
    {
        return $this->belongsTo(Visiteur::class, 'id_visiteur');
    }
}
