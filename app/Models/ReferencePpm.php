<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferencePpm extends Model
{
    use HasFactory;
    protected $table = "reference_ppm";
    public $timestamps = false;
    protected $fillable = [
      'reference'
    ];

    // Pour dire que une reference ppm peut contenir beaucoup d'appel d'offre
    public function appelsOffres() {
        return $this->hasMany(AppelOffreTable::class, 'id_reference', 'id');
    }

}
