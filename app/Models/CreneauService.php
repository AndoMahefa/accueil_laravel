<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreneauService extends Model
{
    use HasFactory;
    protected $table = "creneau_service";
    public $timestamps = false;
    protected $fillable = [
        'jour',
        'heure',
        'heure_fin',
        'id_service',
        'id_direction'
    ];
}
