<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// class RoleService extends Model {
//     use HasFactory;

//     public $timestamps = false;
//     protected $table = "role_service";
//     protected $fillable = [
//         'role',
//         'id_service'
//     ];

//     // Pour dire qu'une role est attribuée à un service
//     public function service() {
//         return $this->belongsTo(Service::class, 'id_service');
//     }

//     // Une role est associée a un ou plusieurs employes
//     public function employes() {
//         return $this->belongsToMany(Employe::class,'role_employe', 'id_role', 'id_employe');
//     }
// }
