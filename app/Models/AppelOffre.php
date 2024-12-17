<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppelOffre extends Model
{
    use HasFactory;

    protected $table = 'appel_offre';
    public $timestamps = false;
    protected $fillable = [
        'titre',
        'description',
        'date_lancement',
        'date_limite',
        'budget_estime',
        'status',
    ];
}
