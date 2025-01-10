<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppelOffreChamps extends Model
{
    use HasFactory;
    protected $table = 'appel_offre_champs';
    public $timestamps = false;
    protected $fillable = [
        'nom_champ',
        'type_champ',
        'options'
    ];

    protected $casts = [
        'options' => 'array'
    ];
}
