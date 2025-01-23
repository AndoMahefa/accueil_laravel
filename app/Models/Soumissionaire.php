<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Soumissionaire extends Model
{
    use HasFactory;
    protected $table = 'soumissionaire';
    public $timestamps = false;
    protected $fillable = [
        'nom',
        'prenom',
        'entreprise',
        'nif_stat',
        'adresse_siege',
        'contact',
        'rcs',
        'fiscale'
    ];

    /**
     * Get all remise offres for this soumissionaire
     */
    public function remiseOffres(): HasMany {
        return $this->hasMany(RemiseOffre::class, 'id_soumissionaire');
    }
}
