<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller {
    public function adminRegister(Request $request) {
        $validated = $request->validate([
            'nom_service' => 'required|string',
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_de_naissance' => 'required|date',
            'adresse' => 'required|string|max:75',
            'cin' => 'required|string|max:25|unique:employe,cin',
            'telephone' => 'required|string|max:25|unique:employe,telephone',
            'genre' => 'required|string|max:20',
            'email' => 'required|string|email',
            'mot_de_passe' => 'required|string'
        ]);

        $idService = Service::insertGetId([
            'nom'=>$validated['nom_service']
        ]);

        $idEmp = Employe::insertGetId([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'date_de_naissance' => $validated['date_de_naissance'],
            'adresse' => $validated['adresse'] ,
            'cin' => $validated['cin'],
            'telephone' => $validated['telephone'],
            'genre' => $validated['genre'],
            'id_service' => $idService
        ]);
        $validated['role'] = 'admin';
        $validated['id_employe'] = $idEmp;

        $validated['mot_de_passe'] = bcrypt($validated['mot_de_passe']);

        $adminUser = User::create([
            'email' => $validated['email'],
            'mot_de_passe' => $validated['mot_de_passe'],
            'id_employe' => $validated['id_employe'],
            'role' => $validated['role']
        ]);

        return response()->json([
            'message' => 'admin creer avec succes',
            'admin' => $adminUser
        ]);
    }
}
