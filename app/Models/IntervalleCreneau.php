<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntervalleCreneau extends Model
{
    use HasFactory;
    protected $table = 'intervalle_creneaux';
    public $timestamps = false;
    protected $fillable = [
        'intervalle',
        'id_direction',
        'id_service'
    ];
}
