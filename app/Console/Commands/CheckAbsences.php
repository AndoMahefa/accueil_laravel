<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employe;
use App\Models\Pointage;
use App\Models\Statut;
use Carbon\Carbon;

class CheckAbsences extends Command
{
    protected $signature = 'absences:check';
    protected $description = 'Marque automatiquement les employés absents';

    public function handle()
    {
        $today = Carbon::today()->toDateString();
        $statutAbsent = Statut::where('statut', 'Absent')->firstOrFail();
        // Récupérer tous les employés
        $employes = Employe::all();

        foreach ($employes as $employe) {
            // Vérifier s'il y a déjà un pointage aujourd'hui
            $pointageExist = Pointage::where('id_employe', $employe->id)
                ->whereDate('date', $today)
                ->exists();

            // Si aucun pointage ET pas de congé, marquer absent
            if (!$pointageExist) {
                Pointage::create([
                    'date' => $today,
                    'heure_arrivee' => null,
                    'heure_depart' => null,
                    'session' => 1,
                    'id_employe' => $employe->id,
                    'id_statut' => $statutAbsent->id
                ]);
                $this->info("Absence enregistrée pour {$employe->nom}");
            }
        }

        $this->info('Vérification des absences terminée !');
    }
}
