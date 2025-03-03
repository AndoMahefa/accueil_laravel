<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps = false;
    protected $table = "utilisateur";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'mot_de_passe',
        'role',
        'id_employe'
    ];

    public function getAuthPassword() {
        return $this->mot_de_passe;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'mot_de_passe'
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'mot_de_passe' => 'hashed',
    ];

    public function employe() {
        return $this->belongsTo(Employe::class, 'id_employe');
    }

    // Dans le modèle Utilisateur
    public function fonctionnalites() {
        return $this->belongsToMany(Fonctionnalite::class, 'role_utilisateur', 'id_utilisateur', 'id_fonctionnalite');
    }
}
