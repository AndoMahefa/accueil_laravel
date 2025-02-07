<?php

namespace App\Http\Controllers;

use App\Models\Direction;
use App\Models\RoleService;
use App\Services\ServiceManager;
use App\Services\TicketService;
use App\Services\VisiteurService;
use Carbon\Carbon;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    protected ServiceManager $serviceManager;
    protected VisiteurService $visiteurService;
    protected TicketService $ticketService;

    public function __construct(ServiceManager $serviceManager, VisiteurService $visiteurService, TicketService $ticketService)
    {
        $this->serviceManager = $serviceManager;
        $this->visiteurService = $visiteurService;
        $this->ticketService = $ticketService;
    }

    public function getAllServicesExceptAccueil()
    {
        $services = Service::whereRaw('LOWER(nom) != ?', ['accueil'])->get();

        return response()->json($services);
    }

    public function index($idService) {
        return response()->json($this->serviceManager->findAll($idService));
    }

    public function findAll() {
        $services = Service::all();
        return response()->json([
            'services' => $services
        ]);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'id_direction' => 'required|exists:direction,id'
        ]);

        $service = $this->serviceManager->create($validated);
        return response()->json($service, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'id_direction' => 'required|exists:direction,id'
        ]);

        $service = $this->serviceManager->update($id, $validated);
        return response()->json($service);
    }


    public function show($id)
    {
        return response()->json($this->serviceManager->findById($id));
    }

    public function destroy($id)
    {
        $this->serviceManager->delete($id);
        return response()->json(['message'=>'service supprime avec succes'], 204);
    }

    public function getDeletedServices()
    {
        $deletedServices = Service::onlyTrashed()->paginate(10);

        return response()->json([
            'message' => 'Services supprimés récupérés avec succès',
            'services' => $deletedServices
        ]);
    }

    public function restore($id)
    {
        $service = Service::withTrashed()->find($id);

        if (!$service) {
            return response()->json(['message' => 'Service introuvable'], 404);
        }

        if ($service->trashed()) {
            $service->restore();
            return response()->json(['message' => 'Service restauré avec succès']);
        }

        return response()->json(['message' => 'Ce service n\'était pas supprimé']);
    }


    public function associeVisiteur(Request $request)
    {
        $donnees = $request->validate([
            'id_visiteur' => 'required|int|exists:visiteur,id',
            'id_direction' => 'required|int|exists:direction,id',
            'id_service' => 'nullable|int|exists:service,id',
            'id_fonction' => 'required|int|exists:fonction,id',
            'motif_visite' => 'required|string'
        ]);

        // Vérifiez si 'id_service' est présent dans les données
        if (!array_key_exists('id_service', $donnees)) {
            $donnees['id_service'] = null; // Ou gérez cela comme vous le souhaitez
        }

        $visiteur = $this->visiteurService->findById($donnees['id_visiteur']);

        $donnees['date_heure_arrivee'] = Carbon::now();
        $donnees['statut'] = 0;

        $motif_visite = $donnees['motif_visite'];
        $statut = $donnees['statut'];

        $direction = Direction::findOrFail($donnees['id_direction']);
        $direction->visiteurs()->attach($visiteur->id, ['motif_visite' => $motif_visite, 'statut' => $statut, 'date_heure_arrivee' => $donnees['date_heure_arrivee'], 'id_service' => $donnees['id_service'], 'id_fonction'=>$donnees['id_fonction']]);

        return response()->json([
            'message' => 'Visiteur associé au service avec succès.',
            'visiteur' => $visiteur,
            'direction' => $direction
        ], 201);
        return response()->json($donnees);
    }

    public function demandeVisiteursParService($idService)
    {
        // Trouver le service par ID
        $service = $this->serviceManager->findById($idService);

        if (!$service) {
            return response()->json(['message' => 'Service non trouvé.'], 404);
        }

        $today = Carbon::now()->toDateString();
        // Récupérer les visiteurs avec un statut de 0
        $visiteurs = $service->visiteurs()
            ->wherePivot('statut', 0)
            ->wherePivot('date_heure_arrivee', 'like', "$today%")
            ->get();

        return response()->json([
            'message' => 'Liste des visiteurs avec statut 0.',
            'visiteurs' => $visiteurs,
        ], 200);
    }

    public function genererTicket(Request $request)
    {
        // Valider les données reçues
        $donnees = $request->validate([
            'temps_estime' => 'required|regex:/^\d{2}:\d{2}/', // Validation du champ temps_estime
            'id_direction' => 'required|int|exists:direction,id',
            // 'id_service' => 'nullable|int|exists:service,id',
            'id_visiteur' => 'required|exists:visiteur,id'
        ]);

        // Récupérer le service et le visiteur
        $direction = Direction::findOrFail($donnees['id_direction']);
        // $service = $this->serviceManager->findById($donnees['id_service']);
        $visiteur = $this->visiteurService->findById($donnees['id_visiteur']);

        // if (!$service || !$visiteur) {
        //     return response()->json(['message' => 'Service ou Visiteur non trouvé'], 404);
        // }

        if (!$direction || !$visiteur) {
            return response()->json(['message' => 'Direction ou Visiteur non trouvé'], 404);
        }

        // Vérifier si le visiteur est déjà associé au service
        // $pivot = $service->visiteurs()->wherePivot('id_visiteur', $donnees['id_visiteur'])->first();
        $pivot = $direction->visiteurs()->wherePivot('id_visiteur', $donnees['id_visiteur'])->first();

        if (!$pivot) {
            return response()->json(['message' => 'Le visiteur n\'est pas associé à ce service'], 404);
        }

        // Récupérer le dernier ticket généré pour ce service
        $dernierTicket = $this->ticketService->getLastTicketForService($donnees['id_direction']);

        // Calculer l'heure prévue pour le nouveau ticket
        $heureValidation = Carbon::now(); // Heure actuelle
        Log::info("Heure validation initial: " . $heureValidation);
        Log::info("Temps estime: " . $donnees['temps_estime']);
        $tempsEstime = Carbon::createFromFormat('H:i', $donnees['temps_estime']);
        if ($dernierTicket) {
            $dernierTicketHeurePrevu = Carbon::createFromFormat('H:i', substr($dernierTicket->heure_prevu, 0, 5));
            $dernierTicketTempsEstime = Carbon::createFromFormat('H:i', substr($dernierTicket->temps_estime, 0, 5));
            Log::info($dernierTicketHeurePrevu);
            Log::info("Heure prevu du dernier ticket: " . $dernierTicket->heure_prevu);
            if ($dernierTicketHeurePrevu > $heureValidation) {
                $heurePrevue = $dernierTicketHeurePrevu
                    ->addMinutes($tempsEstime->hour * 60 + $tempsEstime->minute);
                $tempsEstime = $dernierTicketTempsEstime->addMinutes($tempsEstime->hour * 60 + $tempsEstime->minute);
            } else {
                $heurePrevue = $heureValidation->copy()->addMinutes($tempsEstime->hour * 60 + $tempsEstime->minute);
            }
            Log::info("Heure prevu apres calcul: " . $heurePrevue);
        } else {
            $heurePrevue = $heureValidation->copy()->addMinutes($tempsEstime->hour * 60 + $tempsEstime->minute);
            Log::info("Heure prevu si il n'y a pas de dernier ticket: " . $heureValidation->copy()->addMinutes($tempsEstime->hour * 60 + $tempsEstime->minute));
        }

        // Mettre à jour le statut dans la table pivot
        // $service->visiteurs()->updateExistingPivot($donnees['id_visiteur'], [
        //     'statut' => 1, // Met le statut à "accepté"
        // ]);
        $direction->visiteurs()->updateExistingPivot($donnees['id_visiteur'], [
            'statut' => 1, // Met le statut à "accepté"
        ]);

        // // Créer le ticket avec les détails nécessaires
        $ticketData = [
            'temps_estime' => $tempsEstime,
            'id_direction' => $donnees['id_direction'],
            'id_visiteur' => $donnees['id_visiteur'],
            'date' => $heureValidation->toDateString(),
            'heure_prevu' => $heurePrevue->toTimeString(),
            'heure_validation' => $heureValidation
        ];

        $ticket = $this->ticketService->create($ticketData);

        // Retourner la réponse avec le ticket généré
        // return response()->json([
        //     'message' => 'Ticket généré et statut du visiteur mis à jour avec succès.',
        //     'ticket' => $ticket,
        //     'visiteur' => $visiteur,
        //     'service' => $service
        // ], 200);
        return response()->json([
            'message' => 'Ticket généré et statut du visiteur mis à jour avec succès.',
            'ticket' => $ticket,
            'visiteur' => $visiteur,
            'direction' => $direction
        ], 200);
    }

    public function refuserDemande(Request $request)
    {
        $donnees = $request->validate([
            'id_visiteur' => 'required|exists:visiteur,id',
            'id_service' => 'required|exists:service,id'
        ]);

        $idService = $donnees['id_service'];
        $service = $this->serviceManager->findById($idService);
        $idVisiteur = $donnees['id_visiteur'];
        $visiteur = $this->visiteurService->findById($idVisiteur);

        if (!$service || !$visiteur) {
            return response()->json(['message' => 'Service ou Visiteur non trouvé'], 404);
        }

        $pivot = $service->visiteurs()->wherePivot('id_visiteur', $idVisiteur)->first();

        if (!$pivot) {
            return response()->json(['message' => 'Le visiteur n\'est pas associé à ce service'], 404);
        }

        // Mettre à jour le statut dans la table pivot
        $service->visiteurs()->updateExistingPivot($idVisiteur, [
            'statut' => 2, // Met le statut à "accepté"
        ]);

        return response()->json([
            'message' => 'Demande refuse',
            'visiteur' => $visiteur,
            'service' => $service
        ], 204);
    }

    public function assignRoleToService(Request $request) {
        // Validation des données de la requête
        $validated = $request->validate([
            'role' => 'required|string|max:100', // Vérifie le rôle
            'id_service' => 'required|int|exists:service,id'
        ]);

        // Vérification si le rôle existe déjà pour ce service
        $existingRole = RoleService::where('id_service', $validated['id_service'])
            ->where('role', $validated['role'])
            ->first();

        if ($existingRole) {
            // Si le rôle existe déjà pour ce service, retourner une réponse avec une erreur
            return response()->json(['message' => 'Ce rôle est déjà attribué à ce service'], 400);
        }

        // Créer un nouveau rôle pour ce service
        $roleService = new RoleService();
        $roleService->id_service = $validated['id_service'];
        $roleService->role = $validated['role'];
        $roleService->save();

        return response()->json(['message' => 'Rôle attribué au service avec succès'], 200);
    }

    public function getRolesByService($idService) {
        $roles = RoleService::where('id_service', $idService)->get();

        return response()->json(['roles'=> $roles]);
    }

    public function getServicesByDirection($idDirection) {
        $services = Service::where('id_direction', $idDirection)->get();
        return response()->json([
            'status' => 'success',
            'services' => $services
        ]);
    }
}

