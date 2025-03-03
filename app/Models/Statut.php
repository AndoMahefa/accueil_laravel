<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statut extends Model
{
    use HasFactory;

    protected $table = "statut";
    public $timestamps = false;
    protected $fillable = [
        'id',
        'statut'
    ];

    // Relation avec Pointage
    public function pointages()
    {
        return $this->hasMany(Pointage::class, 'id_statut');
    }
}
