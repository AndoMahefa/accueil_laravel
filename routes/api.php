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
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\ReferencePpmController;
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
Route::post('/admin/create', [AdminController::class, 'adminRegister']);

Route::middleware(['auth:sanctum', 'check.token.expiration'])->group(function() {
    Route::middleware('verify-role:admin')->prefix('admin')->group(function(){
        // Route pour crud service
        Route::post('/service/register', [ServiceController::class, 'store']);
        Route::get('/services', [ServiceController::class, 'findAll']);
        Route::put('/service/{idService}/update', [ServiceController::class, 'update']);
        Route::delete('/service/{idService}/delete', [ServiceController::class, 'destroy']);

        // Attribuer un role a un service
        Route::post('/service/role', [ServiceController::class, 'assignRoleToService']);


        // Route pour crud employe
        Route::get('/service/{idService}/employes', [EmployeController::class, 'findAllByService']);
        Route::post('/employe', [EmployeController::class, 'store']);
        Route::put('/employe/{idEmp}/update', [EmployeController::class, 'update']);
        Route::delete('/employe/{idEmp}/delete', [EmployeController::class, 'destroy']);

        // Creation utilisateur pour un employe
        Route::post('/employe/create-compte', [EmployeController::class, 'createUserForEmploye']);

        //Attribuer des roles a un employe
        Route::post('/employe/role', [EmployeController::class, 'assignRolesToEmployee']);
    });

    Route::middleware('verify-role:user')->prefix('user')->group(function() {

    });
});


// Route pour rendez-vous cote client donc sans authentification
Route::post('/rendez-vous/register', [RendezVousController::class, 'store']);
Route::get('/visiteur/search', [VisiteurController::class, 'search']);
Route::get('/services', [ServiceController::class, 'findAll']);
Route::post('/visiteur', [VisiteurController::class, 'store']);
Route::get('/rdv/heure-indisponible', [RendezVousController::class, 'findHeureIndispo']);

// Route pour appel d'offre cote client donc sans authentification
Route::get('/appel-offre', [AppelOffreController::class, 'index']);

// Route::middleware(['auth:sanctum', 'check.token.expiration'])->group(function () {
//     Route::get('service/{id}', [ServiceController::class, 'show']);

//     Route::middleware('role:Accueil')->group(function () {
//         Route::apiResource('accueil/visiteurs', VisiteurController::class);
//         Route::get('accueil/services/{idService}', [ServiceController::class, 'index']);
//         Route::post('accueil/associe-visiteur-service', [ServiceController::class, 'associeVisiteur']);
//         Route::post('accueil/file-d\'attente', [TicketController::class, 'ticketsLeJourJ']);
//     });
//     Route::middleware('role:Ressource Humaine|Directeur General|Daf|PRMP')->group(function () {
//         Route::get('services/{id}/demandes', [ServiceController::class, 'demandeVisiteursParService']);
//         Route::post('/service/generer-ticket', [ServiceController::class, 'genererTicket']);
//         Route::post('/service/refuser-demande', [ServiceController::class, 'refuserDemande']);
//         Route::post('/service/file-d\'attente', [TicketController::class, 'ticketsLeJourJ']);
//         Route::post('/service/creneaux-register', [CreneauServiceController::class, 'store']);
//         Route::get('/service/creneaux/{idService}', [CreneauServiceController::class, 'findAllService']);
//         Route::delete('/service/{idService}/delete-creneaux/{id}', [CreneauServiceController::class, 'destroy']);
//         Route::get('/service/{idService}/jours-disponible', [RendezVousController::class, 'jourDispoService']);
//         Route::get('/service/{idService}/creneaux/{dayOfWeek}', [RendezVousController::class, 'findCreneauxServiceJour']);
//         Route::get('/service/{idService}/rendez-vous', [RendezVousController::class, 'findRdvByService']);
//     });
//     Route::middleware('role:Ressource Humaine')->group(function () {});
//     Route::middleware('role:Directeur General')->group(function () {
//         Route::apiResource('directeur-general/services', ServiceController::class);
//     });
//     Route::middleware('role:Daf')->group(function () {});
//     Route::middleware('role:PRMP')->group(function () {
//         Route::post('/prmp/appel-offre', [AppelOffreController::class, 'store']);
//         Route::get('/prmp/appel-offre', [AppelOffreController::class, 'index']);

//         Route::get('/prmp/appel-offre-champs', [AppelOffreChampsController::class, 'getFields']);
//         Route::post('/prmp/ajout-champ', [AppelOffreChampsController::class, 'store']);
//         Route::post('/prmp/appel-offre-donnees', [AppelOffreChampsController::class, 'saveDonneesChamps']);
        // Route::put('/prmp/modif-champ-appel/{idChamp}', [AppelOffreChampsController::class, 'update']);
//         Route::delete('/prmp/delete-champ/{idChamp}', [AppelOffreChampsController::class, 'destroy']);
//         Route::get('/prmp/appels-offres', [AppelOffreChampsController::class, 'allAppels']);
//         Route::get('/prmp/appels-offres/{id}', [AppelOffreChampsController::class, 'detailsAppel']);
//         Route::delete('/prmp/appel-offre/{id}/delete', [AppelOffreChampsController::class, 'deleteAppelOffre']);

//         Route::get('/prmp/references', [ReferencePpmController::class, 'index']);
//         Route::post('/prmp/reference', [ReferencePpmController::class, 'store']);
//     });
// });
