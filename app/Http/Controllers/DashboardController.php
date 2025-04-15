<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $visiteursService = DB::table('v_nb_visiteurs_service')->get();
        $visiteursDirection = DB::table('v_nb_visiteurs_direction')->get();
        // $visiteursParPeriodes = DB::table('v_visites_par_periode_detail')->where('jour', '<=', Carbon::now())->orderBy('jour', 'desc')->get();
        $visiteursParPeriodes = DB::table('v_visites_par_periode_detail')->get();
        $frequentationVisiteurs = DB::table('v_type_visiteurs')->get();
        $comparaisonVisiteurs = DB::table('v_comparaison_rdv_sans_rdv')->get();
        $presenceJournalierEmp = DB::table('v_presence_journaliere')->get();
        $retardataires = DB::table('v_retards_frequents')->get();
        $effectifsDirection = DB::table('v_effectif_par_direction')->get();
        $occupationsService = DB::table('v_occupation_services')->get();
        $heuresMoyennesService = DB::table('v_heures_moyennes')->get();
        $heuresMoyennesDirection = DB::table('v_heures_moyennes_direction')->get();

        return response()->json([
            'visiteurs_par_service' => $visiteursService,
            'visiteurs_par_direction' => $visiteursDirection,
            'visiteurs_par_periodes' => $visiteursParPeriodes,
            'frequentation_visiteurs' => $frequentationVisiteurs,
            'type_visiteurs' => $comparaisonVisiteurs,
            'presence_journalier_emp' => $presenceJournalierEmp,
            'retardataires' => $retardataires,
            'effectif_par_direction' => $effectifsDirection,
            'occupations_service' => $occupationsService,
            'heures_moyennes_service' => $heuresMoyennesService,
            'heures_moyennes_direction' => $heuresMoyennesDirection,
        ]);
    }
}
