<?php

namespace App\Http\Controllers;

use App\Models\Direction;
use App\Models\Employe;
use App\Models\Fonction;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller {
    public function adminRegister(Request $request) {
        $validated = $request->validate([
            'directionChoice' => 'required|string|in:existing,new',
            'serviceChoice' => 'required|string|in:existing,new,none',
            'fonctionChoice' => 'required|string|in:existing,new',
            'observation' => 'required|int|exists:observation,id',

            // Direction
            'selectedDirection' => 'required_if:directionChoice,existing|nullable|exists:direction,id',
            'nom_direction' => 'required_if:directionChoice,new|nullable|string|max:100',

            // Service
            'service_id' => 'required_if:serviceChoice,existing|nullable|exists:service,id',
            'nom_service' => 'required_if:serviceChoice,new|nullable|string|max:100',

            // Fonction
            'fonction_id' => 'required_if:fonctionChoice,existing|nullable|exists:fonction,id',
            'nom_fonction' => 'required_if:fonctionChoice,new|nullable|string|max:100',

            // Informations employé
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_de_naissance' => 'required|date',
            'adresse' => 'required|string|max:75',
            'cin' => 'required|string|max:25|unique:employe,cin',
            'telephone' => 'required|string|max:25|unique:employe,telephone',
            'genre' => 'required|string|in:Homme,Femme',

            // Informations compte
            'email' => 'required|email|unique:utilisateur,email',
            'mot_de_passe' => 'required|string|min:6'
        ]);

        // Gestion de la direction
        $idDirection = null;
        if ($validated['directionChoice'] === 'new') {
            $direction = Direction::create(['nom' => $validated['nom_direction']]);
            $idDirection = $direction->id;
        } else {
            $idDirection = $validated['selectedDirection'];
        }

        // Gestion du service
        $idService = null;
        if ($validated['serviceChoice'] === 'new') {
            $service = Service::create([
                'nom' => $validated['nom_service'],
                'id_direction' => $idDirection
            ]);
            $idService = $service->id;
        } elseif ($validated['serviceChoice'] === 'existing') {
            $idService = $validated['service_id'];
        }

        // Gestion de la fonction
        $idFonction = null;
        if ($validated['fonctionChoice'] === 'new') {
            $fonction = Fonction::create([
                'nom' => $validated['nom_fonction'],
                'id_service' => $idService,
                'id_direction' => $idDirection
            ]);
            $idFonction = $fonction->id;
        } else {
            $idFonction = $validated['fonction_id'];
        }

        // Création de l'employé
        $employe = Employe::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'date_de_naissance' => $validated['date_de_naissance'],
            'adresse' => $validated['adresse'],
            'cin' => $validated['cin'],
            'telephone' => $validated['telephone'],
            'genre' => $validated['genre'],
            'id_service' => $idService,
            'id_direction' => $idDirection,
            'id_fonction' => $idFonction,
            'id_observation' => $validated['observation']
        ]);

        // Création du compte utilisateur
        $user = User::create([
            'email' => $validated['email'],
            'mot_de_passe' => bcrypt($validated['mot_de_passe']),
            'id_employe' => $employe->id,
            'role' => 'admin'
        ]);

        return response()->json([
            'message' => 'Administrateur créé avec succès',
            'admin' => $user
        ], 201);
    }
}
