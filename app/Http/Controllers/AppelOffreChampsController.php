<?php

namespace App\Http\Controllers;

use App\Services\AppelOffreChampsService;
use App\Models\AppelOffreDonnees;
use App\Models\AppelOffreTable;
use App\Models\AppelOffreChamps;
use App\Models\Soumissionaire;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppelOffreChampsController extends Controller
{
    protected AppelOffreChampsService $appel;
    public function __construct(AppelOffreChampsService $appel) {
        $this->appel = $appel;
    }

    public function getFields() {
        return $this->appel->findall();
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'nom_champ' => 'required|string|max:100',
            'type_champ' => 'required|string|max:50',
            'options' => 'nullable|array',
        ]);

        return $this->appel->create($validated);
    }

    public function update($id, Request $request) {
        $validated = $request->validate([
            'nom_champ' => 'required|string|max:100',
            'type_champ' => 'required|string|max:50',
            'options' => 'nullable|array',
        ]);

        $appel = $this->appel->update($id, $validated);
        return response()->json($appel);
    }

    public function saveDonneesChamps(Request $request) {
        $validated = $request->validate([
            'data' => 'required|array',
            'data.*.id_appel_offre_champs' => 'required|integer|exists:appel_offre_champs,id',
            'data.*.valeur' => 'required|string',
            'id_reference' => 'required|int'
        ]);

        $champObjet = AppelOffreChamps::where('nom_champ', 'Objet')->first();
        $objet = null;
        $i = 0;
        if ($champObjet) {
            $idChampObjet = $champObjet->id;

            foreach ($validated['data'] as $donnee) {
                if ($donnee['id_appel_offre_champs'] == $idChampObjet) {
                    $objet = $donnee['valeur'];
                    break;
                }
            }
        } else {
            $objet = "Appel d'offre ".$i;
            $i++;
        }


        $appelOffre = AppelOffreTable::insertGetId([
            'appel_offre' => $objet,
            'id_reference' => $validated['id_reference']
        ]);
        // Valider les données

        // Insérer les données
        foreach ($validated['data'] as $donnee) {
            AppelOffreDonnees::create([
                'id_appel_offre_champs' => $donnee['id_appel_offre_champs'],
                'valeur' => $donnee['valeur'],
                'id_appel_offre' => $appelOffre
            ]);
        }

        return response()->json(['message' => 'Données enregistrées avec succès'], 201);
    }

    public function allAppels(Request $request) {
        // Récupérer la référence à filtrer depuis la requête
        $reference = $request->input('reference');

        // Charger les appels d'offres avec leurs données et champs associés
        $query = AppelOffreTable::with(['donnees.champ', 'reference']);

        // Filtrer par référence si spécifiée
        if ($reference) {
            $query->whereHas('reference', function ($q) use ($reference) {
                $q->where('reference', 'like', '%' . $reference . '%');
            });
        }

        // Exclure les appels d'offres marqués comme supprimés (soft delete)
        $query->withoutTrashed();

        $appelsOffres = $query->get();

        // Transformer les données pour inclure "nature" et "objet"
        $result = $appelsOffres->map(function ($appelOffre) {
            $nature = $appelOffre->donnees->firstWhere('champ.nom_champ', 'Nature');
            $objet = $appelOffre->donnees->firstWhere('champ.nom_champ', 'Objet');
            $datePublication = $appelOffre->date_publication;

            return [
                'id' => $appelOffre->id,
                'appel_offre' => $appelOffre->appel_offre,
                'reference' => $appelOffre->reference ? $appelOffre->reference->reference : null,
                'nature' => $nature ? $nature->valeur : null,
                'objet' => $objet ? $objet->valeur : null,
                'date_publication' => $datePublication ? $datePublication : null,
            ];
        });

        return response()->json($result);
    }

    public function deletedAppels(Request $request) {
        // Charger les appels d'offres avec leurs données et champs associés
        $query = AppelOffreTable::with(['donnees.champ', 'reference']);

        // Exclure les appels d'offres marqués comme supprimés (soft delete)
        $query->onlyTrashed();

        $appelsOffres = $query->get();

        // Transformer les données pour inclure "nature" et "objet"
        $result = $appelsOffres->map(function ($appelOffre) {
            $nature = $appelOffre->donnees->firstWhere('champ.nom_champ', 'Nature');
            $objet = $appelOffre->donnees->firstWhere('champ.nom_champ', 'Objet');

            return [
                'id' => $appelOffre->id,
                'appel_offre' => $appelOffre->appel_offre,
                'deleted_at' => $appelOffre->deleted_at,
                'reference' => $appelOffre->reference ? $appelOffre->reference->reference : null,
                'nature' => $nature ? $nature->valeur : null,
                'objet' => $objet ? $objet->valeur : null,
            ];
        });

        return response()->json($result);
    }

    public function restore($id)
    {
        $appelOffre = AppelOffreTable::withTrashed()->find($id);

        if (!$appelOffre) {
            return response()->json(['message' => 'Appel d\'offre introuvable'], 404);
        }

        if ($appelOffre->trashed()) {
            $appelOffre->restore();
            return response()->json(['message' => 'Appel d\'offre restauré avec succès']);
        }

        return response()->json(['message' => 'Cet appel d\'offre n\'était pas supprimé']);
    }

    public function detailsAppel($id) {
        // Charger l'appel d'offres avec ses données et champs associés
        $appelOffre = AppelOffreTable::with(['donnees.champ'])->find($id);

        if (!$appelOffre) {
            return response()->json(['message' => 'Appel d\'offres introuvable'], 404);
        }

        // Préparer les données détaillées
        $details = $appelOffre->donnees->mapWithKeys(function ($donnee) {
            return [$donnee->champ->nom_champ => $donnee->valeur];
        });

        return response()->json([
            'appel_offre' => $appelOffre->appel_offre,
            'reference_ppm' => $appelOffre->reference->reference,
            'details' => $details,
        ]);
    }

    public function deleteAppelOffre($id) {
        // Trouver l'appel d'offre par son ID
        $appelOffre = AppelOffreTable::find($id);

        if (!$appelOffre) {
            return response()->json(['message' => 'Appel d\'offre non trouvé'], 404);
        }

        // Appliquer le soft delete
        $appelOffre->delete();

        return response()->json(['message' => 'Appel d\'offre supprimé avec succès'], 200);
    }

    public function publierAppelOffre(Request $request, $id)
    {
        // Validation des données
        $validated = $request->validate([
            'date_ouverture_plis' => 'nullable|date',
            'heure_limite' => 'nullable|date_format:H:i',
        ]);

        // Recherche de l'appel d'offre
        $appelOffre = AppelOffreTable::find($id);

        if (!$appelOffre) {
            return response()->json(['message' => 'Appel d\'offre introuvable'], 404);
        }

        // Mise à jour des colonnes
        $appelOffre->update([
            'date_publication' => Carbon::now(), // Date actuelle pour date_publication
            'date_ouverture_plis' => $validated['date_ouverture_plis'] ?? $appelOffre->date_ouverture_plis,
            'heure_limite' => $validated['heure_limite'] ?? $appelOffre->heure_limite,
        ]);

        return response()->json(['message' => 'Mise à jour effectuée avec succès', 'appel_offre' => $appelOffre], 200);
    }

    public function getPublishedOffers()
    {
        $appelsOffres = AppelOffreTable::publies()
            ->orderBy('date_publication', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $appelsOffres
        ]);
    }

    public function getSoumissionaire(Request $request)
    {
        // Récupérer l'id_appel_offre depuis la requête
        $idAppelOffre = $request->input('id_appel_offre');

        // Construire la requête avec un filtrage conditionnel
        $query = Soumissionaire::with(['remiseOffres.appelOffre']);

        // Appliquer le filtre si id_appel_offre est fourni
        if ($idAppelOffre) {
            $query->whereHas('remiseOffres', function ($q) use ($idAppelOffre) {
                $q->where('id_appel_offre', $idAppelOffre);
            });
        }

        // Paginer les résultats
        $soumissionaires = $query->paginate(10);

        return response()->json([
            'status' => 'success',
            'soumissionaires' => $soumissionaires
        ]);
    }

}
