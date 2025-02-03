<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observation extends Model {
    use HasFactory;

    public $timestamps = false;
    protected $table = "observation";
    protected $fillable = [
        'observation'
    ];

    protected $dates = ['deleted_at'];
}
