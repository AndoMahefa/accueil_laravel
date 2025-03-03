<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleUtilisateur extends Model
{
    use HasFactory;

    protected $table = 'role_utilisateur'; // Nom de la table
    // Désactiver l'auto-incrémentation de l'ID
    public $incrementing = false;
    public $timestamps = false; // Désactiver les timestamps si absents

    protected $fillable = [
        'id_fonctionnalite', 'id_utilisateur'
    ];

    // Relation avec la table fonctionnalite
    public function fonctionnalite()
    {
        return $this->belongsTo(Fonctionnalite::class, 'id_fonctionnalite');
    }

    // Relation avec la table utilisateur
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_utilisateur');
    }
}
