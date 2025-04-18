<?php

namespace App\Http\Controllers;

use App\Services\VisiteurService;
use App\Models\Visiteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class VisiteurController extends Controller
{
    protected VisiteurService $visiteurService;

    public function __construct(VisiteurService $visiteurService)
    {
        $this->visiteurService = $visiteurService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Visiteur::select();
        if ($search) {
            $query->where('nom', 'ilike', "%$search%")
                ->orWhere('prenom', 'like', "%$search%")
                ->orWhere('cin', 'ilike', "%$search%")
                ->orWhere('email', 'like', "%$search%");
        }

        $visiteurs = $query->paginate(10);

        return response()->json($visiteurs);
    }

    public function show($id)
    {
        $visiteur = $this->visiteurService->findById($id);

        return response()->json($visiteur);
    }

    public function store(Request $request)
    {
        try {
            $donnees_valides = $request->validate([
                'nom' => 'required|string',
                'prenom' => 'required|string',
                'cin' => 'required|string|max:20|unique:visiteur,cin',
                'email' => 'nullable|string|email|unique:visiteur,email',
                'telephone' => 'nullable|string|max:10|regex:/^[0-9]+$/',
                'genre' => 'required|string|max:20',
                'entreprise' => 'nullable|string|max:150'
            ], [
                'nom.required' => 'Le nom est requis.',
                'prenom.required' => 'Le prénom est requis.',
                'cin.required' => 'Le numéro de CIN est requis.',
                'cin.unique' => 'Le numéro de CIN est déjà utilisé par un autre visiteur.',
                'email.email' => 'L\'adresse e-mail doit être valide.',
                'email.unique' => 'L\'adresse e-mail est déjà utilisée par un autre visiteur.',
                'genre.required' => 'Le genre est requis.',
                'entreprise.max' => 'Le nom de l\'entreprise ne doit pas dépasser 150 caractères.',
                'telephone.max' => 'Le numéro de téléphone ne doit pas dépasser 10 chiffres.',
                'telephone.regex' => 'Le numéro de téléphone doit contenir uniquement des chiffres.',
            ]);

            $visiteur = $this->visiteurService->create($donnees_valides);

            return response()->json($visiteur, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation, veuillez vérifier les données fournies.',
                'errors' => $e->errors(),
            ]);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $donnees_valides = $request->validate([
                'nom' => 'required|string|max:50',
                'prenom' => 'required|string|max:50',
                'cin' => 'required|string|max:20',
                'email' => 'nullable|string|max:50|email|unique:visiteur,email,' . $id,
                'telephone' => 'nullable|string|max:50|regex:/^[0-9]+$/',
                'genre' => 'required|string|max:20',
                'entreprise' => 'nullable|string|max:150'
            ], [
                'nom.required' => 'Le nom est requis.',
                'prenom.required' => 'Le prénom est requis.',
                'cin.required' => 'Le numéro de CIN est requis.',
                'cin.unique' => 'Le numéro de CIN est déjà utilisé par un autre visiteur.',
                'email.email' => 'L\'adresse e-mail doit être valide.',
                'email.unique' => 'L\'adresse e-mail est déjà utilisée par un autre visiteur.',
                'genre.required' => 'Le genre est requis.',
                'entreprise.max' => 'Le nom de l\'entreprise ne doit pas dépasser 150 caractères.',
                'telephone.max' => 'Le numéro de téléphone ne doit pas dépasser 10 chiffres.',
                'telephone.regex' => 'Le numéro de téléphone doit contenir uniquement des chiffres.',
            ]);

            $visiteur = $this->visiteurService->update($id, $donnees_valides);

            return response()->json($visiteur, 204);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation, veuillez vérifier les données fournies.',
                'errors' => $e->errors()
            ]);
        }
    }

    public function destroy($id)
    {
        $visiteur = $this->visiteurService->delete($id);

        return response()->json(null, 204);
    }

    public function search(Request $request)
    {
        $searchKey = $request->query('search');
        if (!$searchKey) {
            return response()->json([], 400);
        }
        Log::info($searchKey);

        $visitor = Visiteur::where('email', $searchKey)
            ->orWhere('cin', $searchKey)
            ->first();

        if ($visitor) {
            return response()->json($visitor);
        } else {
            return response()->json([], 200);
        }
    }
}
