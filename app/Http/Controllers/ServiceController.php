<?php

namespace App\Http\Controllers;

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

    public function index($idService)
    {
        return response()->json($this->serviceManager->findAll($idService));
    }

    public function findAll() {
        $services = Service::all();
        return response()->json([
            'services' => $services
        ]);
    }

    public function donnees_valide(Request $request, $isUpdate = false, $id = null)
    {
        // Si c'est une mise à jour, certains champs peuvent être `nullable`
        $rules = [
            'nom' => 'required|string|max:50',
            'email' => [
                'required',
                'string',
                'email',
                // La règle unique doit ignorer l'ID actuel lors de la mise à jour
                'unique:service,email,' . ($isUpdate ? $id : 'NULL')
            ],
            'telephone' => 'required|string|regex:/^[0-9]+$/|max:50',
        ];

        // Ajout conditionnel de la règle pour le mot de passe (non requis pour update)
        if (!$isUpdate) {
            $rules['mot_de_passe'] = 'required|string|min:8';
        } else {
            $rules['mot_de_passe'] = 'nullable|string|min:8'; // Permet la mise à jour sans changer le mot de passe
        }

        return $request->validate($rules);
    }

    public function store(Request $request)
    {
        $donnees = $this->donnees_valide($request); // Validation pour la création

        // Hachage du mot de passe avant enregistrement
        $donnees['mot_de_passe'] = bcrypt($donnees['mot_de_passe']);

        $service = $this->serviceManager->create($donnees);
        return response()->json($service, 201);
    }

    public function update(Request $request, $id)
    {
        $donnees = $this->donnees_valide($request, true, $id); // Validation pour la mise à jour

        // Si un mot de passe est fourni, on le hache ; sinon, on l'ignore
        if (!empty($donnees['mot_de_passe'])) {
            $donnees['mot_de_passe'] = bcrypt($donnees['mot_de_passe']);
        } else {
            unset($donnees['mot_de_passe']);
        }

        $service = $this->serviceManager->update($id, $donnees);
        return response()->json($service);
    }


    public function show($id)
    {
        return response()->json($this->serviceManager->findById($id));
    }

    public function destroy($id)
    {
        $this->serviceManager->delete($id);
        return response()->json(null, 204);
    }

    public function associeVisiteur(Request $request)
    {
        $donnees = $request->validate([
            'id_visiteur' => 'required|exists:visiteur,id',
            'id_service' => 'required|exists:service,id',
            'motif_visite' => 'required|string'
        ]);

        $visiteur = $this->visiteurService->findById($donnees['id_visiteur']);
        $service = $this->serviceManager->findById($donnees['id_service']);

        $donnees['date_heure_arrivee'] = Carbon::now();
        $donnees['statut'] = 0;
        $service->visiteurs()->attach($visiteur->id, ['motif_visite' => $donnees['motif_visite'], 'statut' => $donnees['statut'], 'date_heure_arrivee' => $donnees['date_heure_arrivee']]);

        return response()->json([
            'message' => 'Visiteur associé au service avec succès.',
            'visiteur' => $visiteur,
            'service' => $service,
        ], 201);
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
            'id_service' => 'required|exists:service,id',
            'id_visiteur' => 'required|exists:visiteur,id'
        ]);

        // Récupérer le service et le visiteur
        $service = $this->serviceManager->findById($donnees['id_service']);
        $visiteur = $this->visiteurService->findById($donnees['id_visiteur']);

        if (!$service || !$visiteur) {
            return response()->json(['message' => 'Service ou Visiteur non trouvé'], 404);
        }

        // Vérifier si le visiteur est déjà associé au service
        $pivot = $service->visiteurs()->wherePivot('id_visiteur', $donnees['id_visiteur'])->first();

        if (!$pivot) {
            return response()->json(['message' => 'Le visiteur n\'est pas associé à ce service'], 404);
        }

        // Récupérer le dernier ticket généré pour ce service
        $dernierTicket = $this->ticketService->getLastTicketForService($donnees['id_service']);

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
        $service->visiteurs()->updateExistingPivot($donnees['id_visiteur'], [
            'statut' => 1, // Met le statut à "accepté"
        ]);

        // // Créer le ticket avec les détails nécessaires
        $ticketData = [
            'temps_estime' => $tempsEstime,
            'id_service' => $donnees['id_service'],
            'id_visiteur' => $donnees['id_visiteur'],
            'date' => $heureValidation->toDateString(),
            'heure_prevu' => $heurePrevue->toTimeString(),
            'heure_validation' => $heureValidation
        ];

        $ticket = $this->ticketService->create($ticketData);

        // Retourner la réponse avec le ticket généré
        return response()->json([
            'message' => 'Ticket généré et statut du visiteur mis à jour avec succès.',
            'ticket' => $ticket,
            'visiteur' => $visiteur,
            'service' => $service
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
}
