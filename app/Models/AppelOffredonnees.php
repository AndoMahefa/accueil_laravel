<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppelOffredonnees extends Model
{
    use HasFactory;
    protected $table = 'appel_offre_donnees';
    public $timestamps = false;
    protected $fillable = [
        'id_appel_offre_champs',
        'valeur',
        'id_appel_offre'
    ];


    // Pour dire qu'une donnée est liée a un appel d'offre et à une champ
    public function appelOffre() {
        return $this->belongsTo(AppelOffreTable::class, 'id_appel_offre');
    }

    public function champ() {
        return $this->belongsTo(AppelOffreChamps::class, 'id_appel_offre_champs');
    }

}
