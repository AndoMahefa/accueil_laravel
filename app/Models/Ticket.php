<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model {
    use HasFactory;

    protected $table = 'ticket';
    protected $fillable = [
        'temps_estime',
        'id_service',
        'id_visiteur'
    ];

    public $timestamps = false;
}
