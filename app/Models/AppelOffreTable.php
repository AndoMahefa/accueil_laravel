<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppelOffreTable extends Model {
    use HasFactory;
    use SoftDeletes;

    protected $table = 'appel_offre_table';
    public $timestamps = false;
    protected $fillable = [
        'appel_offre',
        'id_reference',
        'date_publication',
        'date_ouverture_plis',
        'heure_limite'
    ];

    protected $dates = ['deleted_at'];
    // Pour avoir les donnees d'une appel d'offre
    public function donnees() {
        return $this->hasMany(AppelOffredonnees::class, 'id_appel_offre');
    }

    // Pour dire que chaque appel est lié à une reference
    public function reference() {
        return $this->belongsTo(ReferencePpm::class, 'id_reference', 'id');
    }

    /**
     * Scope pour récupérer uniquement les appels d'offres publiés
     */
    public function scopePublies($query)
    {
        return $query->whereNotNull('date_publication');
    }
}
