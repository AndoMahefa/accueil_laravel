<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Authentification\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\VisiteurController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\CreneauServiceController;
use App\Http\Controllers\AppelOffreController;
use App\Http\Controllers\AppelOffreChampsController;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\FonctionController;
use App\Http\Controllers\FonctionnaliteController;
use App\Http\Controllers\IntervalleCreneauController;
use App\Http\Controllers\ObservationController;
use App\Http\Controllers\PointageController;
use App\Http\Controllers\ReferencePpmController;
use App\Http\Controllers\RemiseOffreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route pour login
Route::post('/login', [AuthController::class, 'login']);

// Route pour rediriger vers creation admin et creation admin
Route::get('/fonctions/service/{idService}', [FonctionController::class, 'getFonctionsByService']);
Route::get('/fonctions/direction/{idDirection}', [FonctionController::class, 'getFonctionsByDirection']);
Route::get('/services/direction/{idDirection}', [ServiceController::class, 'getServicesByDirection']);
Route::get('/observations', [ObservationController::class, 'getObservations']);
Route::get('/directions', [DirectionController::class, 'getDirections']);
Route::post('/admin/create', [AdminController::class, 'adminRegister']);
// Route pour rendez-vous cote client donc sans authentification
Route::post('/rendez-vous/register', [RendezVousController::class, 'store']);
Route::get('/visiteur/search', [VisiteurController::class, 'search']);
Route::post('/visiteur', [VisiteurController::class, 'store']);
Route::get('/services', [ServiceController::class, 'findAll']);
Route::get('/services/except-accueil', [ServiceController::class, 'getAllServicesExceptAccueil']);
Route::get('/service/{idService}/jours-disponible', [RendezVousController::class, 'jourDispoService']);
Route::get('/direction/{idDirection}/jours-disponible', [RendezVousController::class, 'jourDispoDirection']);
Route::get('/service/{idService}/creneaux/{dayOfWeek}', [RendezVousController::class, 'findCreneauxServiceJour']);
Route::get('/direction/{idDirection}/creneaux/{dayOfWeek}', [RendezVousController::class, 'findCreneauxDirectionJour']);
Route::get('/rdv/heure-indisponible/service', [RendezVousController::class, 'findHeureIndispo']);
Route::get('/rdv/heure-indisponible/direction', [RendezVousController::class, 'findHeureIndispoByDirection']);

Route::get('direction/{idDirection}/intervalle', [IntervalleCreneauController::class, 'findByDirection']);
Route::get('service/{idService}/intervalle', [IntervalleCreneauController::class, 'findByService']);

Route::get('service/{id}', [ServiceController::class, 'show']);

// Route pour appel d'offre cote client donc sans authentification
Route::get('/appel-offre', [AppelOffreController::class, 'index']);

Route::get('/items', [FonctionnaliteController::class, 'items']);

Route::middleware(['auth:sanctum', 'check.token.expiration'])->group(function() {
    Route::get('service/{id}', [ServiceController::class, 'show']);

    Route::middleware('verify-role:admin')->prefix('admin')->group(function(){
        Route::get('/fonctionnalites', [FonctionnaliteController::class, 'items']);
        Route::get('/roles/utilisateur/{idUser}', [FonctionnaliteController::class, 'findRoleByUser']);
        Route::post('/fonctionnalite', [FonctionnaliteController::class, 'assignRoleUser']);


        // Route pour crud direction
        Route::post('/direction/register', [DirectionController::class, 'store']);
        Route::get('/directions', [DirectionController::class, 'getDirections']);
        Route::put('/direction/{idDirection}/update', [DirectionController::class, 'update']);
        Route::delete('/direction/{idDirection}/delete', [DirectionController::class, 'delete']);
        // Route::get('/directions/deleted', [DirectionController::class, 'getDeletedDirections']);
        // Route::post('/directions/{id}/restore', [DirectionController::class, 'restore']);


        // Route pour crud service
        Route::post('/service/register', [ServiceController::class, 'store']);
        Route::get('/services', [ServiceController::class, 'findAll']);
        Route::put('/service/{idService}/update', [ServiceController::class, 'update']);
        Route::delete('/service/{idService}/delete', [ServiceController::class, 'destroy']);
        Route::get('/services/deleted', [ServiceController::class, 'getDeletedServices']);
        Route::post('/services/{id}/restore', [ServiceController::class, 'restore']);

        // Attribuer un role a un service
        Route::post('/service/role', [ServiceController::class, 'assignRoleToService']);
        Route::get('/service/{idService}/roles', [ServiceController::class, 'getRolesByService']);


        // Route pour crud observation
        Route::post('/observation/register', [ObservationController::class, 'store']);
        Route::get('/observations', [ObservationController::class, 'getObservations']);
        Route::put('/observation/{idObservation}/update', [ObservationController::class, 'update']);
        Route::delete('/observation/{idObservation}/delete', [ObservationController::class, 'delete']);


        // Route pour crud fonction
        Route::post('/fonction/register', [FonctionController::class, 'store']);
        Route::get('/fonctions', [FonctionController::class, 'getFonctions']);
        Route::put('/fonction/{idFonction}/update', [FonctionController::class, 'update']);
        Route::delete('/fonction/{idFonction}/delete', [FonctionController::class, 'delete']);


        // Route pour crud employe
        Route::get('/employes', [EmployeController::class, 'findEmployes']); //Tous les emmployes
        Route::get('/direction/{idDirection}/employes', [EmployeController::class, 'findEmployesByDirection']); //Tous les employes dans une direction
        Route::get('/service/{idService}/employes', [EmployeController::class, 'findEmployesByService']); // Tous les employes dans une service
        Route::post('/employe', [EmployeController::class, 'store']);
        Route::put('/employe/{idEmp}/update', [EmployeController::class, 'update']);
        Route::delete('/employe/{idEmp}/delete', [EmployeController::class, 'destroy']);
        Route::get('/employes/deleted', [EmployeController::class, 'getDeletedEmployes']);
        Route::post('/employes/{id}/restore', [EmployeController::class, 'restore']);

        // Creation utilisateur pour un employe
        Route::post('/employe/create-compte', [EmployeController::class, 'createUserForEmploye']);

        //Attribuer des roles a un employe
        Route::post('/employe/role', [EmployeController::class, 'assignRolesToEmployee']);
        Route::delete('/employe/{idEmploye}/role/{idRole}', [EmployeController::class, 'deleteRoleEmploye']);
        Route::get('/employe/{idEmploye}/roles', [EmployeController::class, 'getRolesByEmploye']);


        // Route pour le service accueil
        Route::post('accueil/visiteur', [VisiteurController::class, 'store']);
        Route::get('accueil/visiteurs', [VisiteurController::class, 'index']);
        Route::put('accueil/visiteur/{id}', [VisiteurController::class, 'update']);
        Route::delete('accueil/visiteur/{id}', [VisiteurController::class, 'destroy']);

        Route::get('accueil/services/{idService}', [ServiceController::class, 'index']);
        Route::get('accueil/services', [ServiceController::class, 'getAllServicesExceptAccueil']);
        // Route::post('accueil/associe-visiteur-service', [ServiceController::class, 'associeVisiteur']);
        Route::post('accueil/demande-service', [ServiceController::class, 'associeVisiteur']);
        Route::post('accueil/file-d\'attente', [TicketController::class, 'ticketsLeJourJ']);
        Route::get('tickets/{idDirection?}', [TicketController::class, 'ticketsLeJourJ']);
        Route::post('accueil/remise-offre', [RemiseOffreController::class, 'store']);


        // Route pour les Services PRMP/RH/DG/Daf
        // Route::get('services/{id}/demandes', [ServiceController::class, 'demandeVisiteursParService']);
        Route::get('directions/demandes', [DirectionController::class, 'demandeVisiteurs']);
        Route::get('directions/{idDirection}/demandes', [DirectionController::class, 'demandeVisiteursParDirection']);
        Route::post('/service/generer-ticket', [ServiceController::class, 'genererTicket']);
        Route::post('/service/refuser-demande', [ServiceController::class, 'refuserDemande']);
        Route::post('/service/file-d\'attente', [TicketController::class, 'ticketsLeJourJ']);
        Route::post('/service/creneaux-register', [CreneauServiceController::class, 'store']);
        Route::get('/service/{idService}/creneaux', [CreneauServiceController::class, 'findAllService']);
        Route::get('/direction/{idDirection}/creneaux', [CreneauServiceController::class, 'findAllDirection']);
        Route::delete('/service/{idService}/delete-creneaux/{id}', [CreneauServiceController::class, 'destroyByService']);
        Route::delete('/direction/{idDirection}/delete-creneaux/{id}', [CreneauServiceController::class, 'destroyByDirection']);
        Route::get('/service/{idService}/jours-disponible', [RendezVousController::class, 'jourDispoService']);
        Route::get('/service/{idService}/creneaux/{dayOfWeek}', [RendezVousController::class, 'findCreneauxServiceJour']);
        Route::get('/service/{idService}/rendez-vous', [RendezVousController::class, 'findRdvByService']);
        Route::get('/rendez-vous', [RendezVousController::class, 'index']);


        // Route pour le service PRMP
        Route::post('/prmp/appel-offre', [AppelOffreController::class, 'store']);
        Route::get('/prmp/appel-offre', [AppelOffreController::class, 'index']);

        Route::get('/prmp/appel-offre-champs', [AppelOffreChampsController::class, 'getFields']);
        Route::post('/prmp/ajout-champ', [AppelOffreChampsController::class, 'store']);
        Route::post('/prmp/appel-offre-donnees', [AppelOffreChampsController::class, 'saveDonneesChamps']);
        Route::put('/prmp/modif-champ-appel/{idChamp}', [AppelOffreChampsController::class, 'update']);
        Route::delete('/prmp/delete-champ/{idChamp}', [AppelOffreChampsController::class, 'deleteChamps']);
        Route::get('/prmp/appels-offres', [AppelOffreChampsController::class, 'allAppels']);
        Route::get('/prmp/appels-offres/{id}', [AppelOffreChampsController::class, 'detailsAppel']);
        Route::delete('/prmp/appel-offre/{id}/delete', [AppelOffreChampsController::class, 'deleteAppelOffre']);
        Route::get('/prmp/appels-offres-deleted', [AppelOffreChampsController::class, 'deletedAppels']);
        Route::post('/prmp/appel-offre/{id}/restore', [AppelOffreChampsController::class, 'restore']);
        Route::put('/prmp/appel-offre/{id}/publier', [AppelOffreChampsController::class, 'publierAppelOffre']);
        Route::get('/prmp/offre/publies', [AppelOffreChampsController::class, 'getPublishedOffers']);
        Route::get('/prmp/soumissionaires', [AppelOffreChampsController::class, 'getSoumissionaire']);

        Route::get('/prmp/references', [ReferencePpmController::class, 'index']);
        Route::post('/prmp/reference', [ReferencePpmController::class, 'store']);

        Route::get('intervalles', [IntervalleCreneauController::class, 'getIntervalles']);
        Route::post('intervalle', [IntervalleCreneauController::class, 'store']);
        Route::put('intervalle/{idIntervalle}', [IntervalleCreneauController::class, 'update']);
        Route::delete('intervalle/{idIntervalle}', [IntervalleCreneauController::class, 'deleteIntervalle']);
        Route::get('direction/{idDirection}/intervalle', [IntervalleCreneauController::class, 'findByDirection']);
        Route::get('service/{idService}/intervalle', [IntervalleCreneauController::class, 'findByService']);
        Route::get('jour/{idJour}', [DirectionController::class, 'findJour']);

        Route::post('/pointage', [PointageController::class, 'pointerArrivee']);
        Route::post('/pointage/depart', [PointageController::class, 'pointerDepart']);
        Route::get('/pointages', [PointageController::class, 'findAll']);
        Route::get('/pointages/{idEmploye}', [PointageController::class, 'ficheByEmploye']);
        Route::post('pointer/conge', [PointageController::class, 'pointerConge']);
    });

    Route::middleware('verify-role:user')->prefix('user')->group(function(){
        // Route pour crud direction
        Route::post('/direction/register', [DirectionController::class, 'store']);
        Route::get('/directions', [DirectionController::class, 'getDirections']);
        Route::put('/direction/{idDirection}/update', [DirectionController::class, 'update']);
        Route::delete('/direction/{idDirection}/delete', [DirectionController::class, 'delete']);
        // Route::get('/directions/deleted', [DirectionController::class, 'getDeletedDirections']);
        // Route::post('/directions/{id}/restore', [DirectionController::class, 'restore']);


        // Route pour crud service
        Route::post('/service/register', [ServiceController::class, 'store']);
        Route::get('/services', [ServiceController::class, 'findAll']);
        Route::put('/service/{idService}/update', [ServiceController::class, 'update']);
        Route::delete('/service/{idService}/delete', [ServiceController::class, 'destroy']);
        Route::get('/services/deleted', [ServiceController::class, 'getDeletedServices']);
        Route::post('/services/{id}/restore', [ServiceController::class, 'restore']);

        // Attribuer un role a un service
        Route::post('/service/role', [ServiceController::class, 'assignRoleToService']);
        Route::get('/service/{idService}/roles', [ServiceController::class, 'getRolesByService']);


        // Route pour crud observation
        Route::post('/observation/register', [ObservationController::class, 'store']);
        Route::get('/observations', [ObservationController::class, 'getObservations']);
        Route::put('/observation/{idObservation}/update', [ObservationController::class, 'update']);
        Route::delete('/observation/{idObservation}/delete', [ObservationController::class, 'delete']);


        // Route pour crud fonction
        Route::post('/fonction/register', [FonctionController::class, 'store']);
        Route::get('/fonctions', [FonctionController::class, 'getFonctions']);
        Route::put('/fonction/{idFonction}/update', [FonctionController::class, 'update']);
        Route::delete('/fonction/{idFonction}/delete', [FonctionController::class, 'delete']);


        // Route pour crud employe
        Route::get('/employes', [EmployeController::class, 'findEmployes']); //Tous les emmployes
        Route::get('/direction/{idDirection}/employes', [EmployeController::class, 'findEmployesByDirection']); //Tous les employes dans une direction
        Route::get('/service/{idService}/employes', [EmployeController::class, 'findEmployesByService']); // Tous les employes dans une service
        Route::post('/employe', [EmployeController::class, 'store']);
        Route::put('/employe/{idEmp}/update', [EmployeController::class, 'update']);
        Route::delete('/employe/{idEmp}/delete', [EmployeController::class, 'destroy']);
        Route::get('/employes/deleted', [EmployeController::class, 'getDeletedEmployes']);
        Route::post('/employes/{id}/restore', [EmployeController::class, 'restore']);

        // Creation utilisateur pour un employe
        Route::post('/employe/create-compte', [EmployeController::class, 'createUserForEmploye']);

        //Attribuer des roles a un employe
        Route::post('/employe/role', [EmployeController::class, 'assignRolesToEmployee']);
        Route::delete('/employe/{idEmploye}/role/{idRole}', [EmployeController::class, 'deleteRoleEmploye']);
        Route::get('/employe/{idEmploye}/roles', [EmployeController::class, 'getRolesByEmploye']);


        // Route pour le service accueil
        Route::post('accueil/visiteur', [VisiteurController::class, 'store']);
        Route::get('accueil/visiteurs', [VisiteurController::class, 'index']);
        Route::put('accueil/visiteur/{id}', [VisiteurController::class, 'update']);
        Route::delete('accueil/visiteur/{id}', [VisiteurController::class, 'destroy']);

        Route::get('accueil/services/{idService}', [ServiceController::class, 'index']);
        Route::get('accueil/services', [ServiceController::class, 'getAllServicesExceptAccueil']);
        // Route::post('accueil/associe-visiteur-service', [ServiceController::class, 'associeVisiteur']);
        Route::post('accueil/demande-service', [ServiceController::class, 'associeVisiteur']);
        Route::post('accueil/file-d\'attente', [TicketController::class, 'ticketsLeJourJ']);
        Route::get('tickets/{idDirection?}', [TicketController::class, 'ticketsLeJourJ']);
        Route::post('accueil/remise-offre', [RemiseOffreController::class, 'store']);


        // Route pour les Services PRMP/RH/DG/Daf
        // Route::get('services/{id}/demandes', [ServiceController::class, 'demandeVisiteursParService']);
        Route::get('directions/demandes', [DirectionController::class, 'demandeVisiteurs']);
        Route::get('directions/{idDirection}/demandes', [DirectionController::class, 'demandeVisiteursParDirection']);
        Route::post('/service/generer-ticket', [ServiceController::class, 'genererTicket']);
        Route::post('/service/refuser-demande', [ServiceController::class, 'refuserDemande']);
        Route::post('/service/file-d\'attente', [TicketController::class, 'ticketsLeJourJ']);
        Route::post('/service/creneaux-register', [CreneauServiceController::class, 'store']);
        Route::get('/service/{idService}/creneaux', [CreneauServiceController::class, 'findAllService']);
        Route::get('/direction/{idDirection}/creneaux', [CreneauServiceController::class, 'findAllDirection']);
        Route::delete('/service/{idService}/delete-creneaux/{id}', [CreneauServiceController::class, 'destroyByService']);
        Route::delete('/direction/{idDirection}/delete-creneaux/{id}', [CreneauServiceController::class, 'destroyByDirection']);
        Route::get('/service/{idService}/jours-disponible', [RendezVousController::class, 'jourDispoService']);
        Route::get('/service/{idService}/creneaux/{dayOfWeek}', [RendezVousController::class, 'findCreneauxServiceJour']);
        Route::get('/service/{idService}/rendez-vous', [RendezVousController::class, 'findRdvByService']);
        Route::get('/rendez-vous', [RendezVousController::class, 'index']);


        // Route pour le service PRMP
        Route::post('/prmp/appel-offre', [AppelOffreController::class, 'store']);
        Route::get('/prmp/appel-offre', [AppelOffreController::class, 'index']);

        Route::get('/prmp/appel-offre-champs', [AppelOffreChampsController::class, 'getFields']);
        Route::post('/prmp/ajout-champ', [AppelOffreChampsController::class, 'store']);
        Route::post('/prmp/appel-offre-donnees', [AppelOffreChampsController::class, 'saveDonneesChamps']);
        Route::put('/prmp/modif-champ-appel/{idChamp}', [AppelOffreChampsController::class, 'update']);
        Route::delete('/prmp/delete-champ/{idChamp}', [AppelOffreChampsController::class, 'deleteChamps']);
        Route::get('/prmp/appels-offres', [AppelOffreChampsController::class, 'allAppels']);
        Route::get('/prmp/appels-offres/{id}', [AppelOffreChampsController::class, 'detailsAppel']);
        Route::delete('/prmp/appel-offre/{id}/delete', [AppelOffreChampsController::class, 'deleteAppelOffre']);
        Route::get('/prmp/appels-offres-deleted', [AppelOffreChampsController::class, 'deletedAppels']);
        Route::post('/prmp/appel-offre/{id}/restore', [AppelOffreChampsController::class, 'restore']);
        Route::put('/prmp/appel-offre/{id}/publier', [AppelOffreChampsController::class, 'publierAppelOffre']);
        Route::get('/prmp/offre/publies', [AppelOffreChampsController::class, 'getPublishedOffers']);
        Route::get('/prmp/soumissionaires', [AppelOffreChampsController::class, 'getSoumissionaire']);

        Route::get('/prmp/references', [ReferencePpmController::class, 'index']);
        Route::post('/prmp/reference', [ReferencePpmController::class, 'store']);

        Route::get('intervalles', [IntervalleCreneauController::class, 'getIntervalles']);
        Route::post('intervalle', [IntervalleCreneauController::class, 'store']);
        Route::put('intervalle/{idIntervalle}', [IntervalleCreneauController::class, 'update']);
        Route::get('direction/{idDirection}/intervalle', [IntervalleCreneauController::class, 'findByDirection']);
        Route::get('service/{idService}/intervalle', [IntervalleCreneauController::class, 'findByService']);
        Route::get('jour/{idJour}', [DirectionController::class, 'findJour']);

        Route::post('/pointage', [PointageController::class, 'pointerArrivee']);
        Route::post('/pointage/depart', [PointageController::class, 'pointerDepart']);
        Route::get('/pointages', [PointageController::class, 'findAll']);
        Route::get('pointages/{idEmploye}', [PointageController::class, 'ficheByEmploye']);
        Route::post('pointer/conge', [PointageController::class, 'pointerConge']);
    });
});
