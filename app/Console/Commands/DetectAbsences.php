<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employe;
use App\Models\Pointage;
use App\Models\Statut;
use Carbon\Carbon;

class DetectAbsences extends Command
{
    protected $signature = 'absences:detect';
    protected $description = 'Détecte les employés absents à 17h et les enregistre';

    public function handle()
    {
        $statutAbsent = Statut::where('statut', 'Absent')->first();
        $today = Carbon::today();

        // Récupérer les employés qui n'ont pas pointé aujourd'hui
        $absents = Employe::whereDoesntHave('pointages', function ($query) use ($today) {
            $query->whereDate('date', $today);
        })->get();

        // Ajouter une absence pour chaque employé
        foreach ($absents as $employe) {
            Pointage::create([
                'date' => $today,
                'heure_arrivee' => null,
                'heure_depart' => null,
                'session' => 1,
                'employe_id' => $employe->id,
                'statut_id' => $statutAbsent->id
            ]);
        }

        $this->info('Absences détectées et enregistrées.');
    }
}
