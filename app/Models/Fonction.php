<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fonction extends Model {
    use HasFactory;

    public $timestamps = false;
    protected $table = "fonction";
    protected $fillable = [
        'nom',
        'id_service',
        'id_direction'
    ];

    protected $dates = ['deleted_at'];

    public function service() {
        return $this->belongsTo(Service::class, 'id_service');
    }

    public function direction() {
        return $this->belongsTo(Direction::class, 'id_direction');
    }
}
