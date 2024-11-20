<?php

use App\Http\Controllers\Authentification\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\VisiteurController;
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

Route::middleware(['auth:sanctum', 'check.token.expiration'])->group(function(){
    Route::get('service/{id}', [ServiceController::class, 'show']);

    Route::middleware('role:accueil')->group(function(){
        Route::apiResource('accueil/visiteurs', VisiteurController::class);
        Route::get('accueil/services/{idService}', [ServiceController::class, 'index']);
        Route::post('accueil/associe-visiteur-service', [ServiceController::class, 'associeVisiteur']);
    });
    Route::middleware('role:Ressources humaine|Directeur General|Daf')->group(function(){
        Route::get('services/{id}/demandes', [ServiceController::class, 'demandeVisiteursParService']);
        Route::post('/service/generer-ticket', [ServiceController::class, 'genererTicket']);
    });
    Route::middleware('role:Ressources humaine')->group(function(){

    });
    Route::middleware('role:Directeur General')->group(function(){
        Route::apiResource('directeur-general/services', ServiceController::class);
    });
    Route::middleware('role:Daf')->group(function(){

    });
});

Route::post('/login', [AuthController::class, 'login']);