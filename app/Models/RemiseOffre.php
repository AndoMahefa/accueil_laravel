<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RemiseOffre extends Model {
    use HasFactory;

    protected $table = 'remise_offre';
    public $timestamps = false;
    protected $fillable = [
        'date_remise',
        'heure_remise',
        'id_soumissionaire',
        'id_appel_offre'
    ];

    protected $casts = [
        'date_remise' => 'date',
        'heure_remise' => 'datetime'
    ];

    /**
     * Get the soumissionaire that owns the remise offre
     */
    public function soumissionaire(): BelongsTo
    {
        return $this->belongsTo(Soumissionaire::class, 'id_soumissionaire');
    }

    /**
     * Get the appel offre that owns the remise offre
     */
    public function appelOffre(): BelongsTo
    {
        return $this->belongsTo(AppelOffreTable::class, 'id_appel_offre');
    }
}
