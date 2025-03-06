<?php

namespace App\Observers;

use App\Models\Pointage;
use App\Models\Statut;
use Illuminate\Support\Facades\Log;

class PointageObserver
{
    /**
     * Handle the Pointage "created" event.
     */
    public function created(Pointage $pointage): void
    {
        //
    }

    public function creating(Pointage $pointage)
    {
        try {
            // Si le statut est déjà défini, on ne fait rien
            if ($pointage->id_statut !== null) {
                return;
            }
             // Récupérer l'ID du statut "Retard"
            $statutRetard = Statut::where('statut', 'Retard')->first();
            $statutPresent = Statut::where('statut', 'Présent')->first();

            Log::info("statut : " . $statutRetard);
            Log::info("statut present : " . $statutPresent);
            // Vérifier si l'employé a déjà un pointage pour aujourd'hui
            $existingPointage = Pointage::where('id_employe', $pointage->id_employe)
                ->whereDate('date', $pointage->date)
                ->first();

            Log::info($existingPointage);
            // Si l'employé a déjà un pointage, garder le statut "Présent"
            if ($existingPointage && $existingPointage->heure_depart != null) {
                Log::info("id_statut existant : " . $existingPointage->id_statut);
                $pointage->id_statut = $existingPointage->id_statut;
                Log::info($pointage);
                // $pointage = $existingPointage->replicate();

                return ;
                Log::info($pointage);

            }

            if (!$existingPointage) {
                // Vérifier si l'heure d'arrivée est après 08h30
                if ($pointage->heure_arrivee > '08:30:00') {
                    Log::info("retard");
                    $pointage->id_statut = $statutRetard?->id;
                } else {
                    Log::info("present");
                    Log::info("statut present 2 : " . $statutPresent->id);
                    $pointage->id_statut = $statutPresent?->id;
                }

                Log::info($pointage);
            }

            return ;

        } catch (\Throwable $th) {
            throw $th->getMessage();
        }
    }
    /**
     * Handle the Pointage "updated" event.
     */
    public function updated(Pointage $pointage): void
    {
        //
    }

    /**
     * Handle the Pointage "deleted" event.
     */
    public function deleted(Pointage $pointage): void
    {
        //
    }

    /**
     * Handle the Pointage "restored" event.
     */
    public function restored(Pointage $pointage): void
    {
        //
    }

    /**
     * Handle the Pointage "force deleted" event.
     */
    public function forceDeleted(Pointage $pointage): void
    {
        //
    }
}
