<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fonctionnalite extends Model
{
    use HasFactory;

    protected $table = 'fonctionnalite'; // Nom de la table
    protected $primaryKey = 'id'; // Clé primaire
    public $timestamps = false; // Désactiver les timestamps si absents

    protected $fillable = [
        'titre', 'vers', 'icon', 'statut', 'id_fonctionnalite'
    ];

    // Relation avec la fonctionnalité parent (si applicable)
    public function parent()
    {
        return $this->belongsTo(Fonctionnalite::class, 'id_fonctionnalite');
    }

    // Relation avec les fonctionnalités enfants
    public function enfants()
    {
        return $this->hasMany(Fonctionnalite::class, 'id_fonctionnalite');
    }

    // Dans le modèle Fonctionnalite
    public function utilisateurs() {
        return $this->belongsToMany(User::class, 'role_utilisateur', 'id_fonctionnalite', 'id_utilisateur');
    }
}
