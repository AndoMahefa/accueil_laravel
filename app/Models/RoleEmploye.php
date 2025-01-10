<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleEmploye extends Model {
    use HasFactory;

    protected $table = "role_employe";
    public $timestamps = false;
    protected $fillable = [
        'id_employe',
        'id_role'
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class, 'id_employe');
    }

    public function roleService()
    {
        return $this->belongsTo(RoleService::class, 'id_role');
    }
}
