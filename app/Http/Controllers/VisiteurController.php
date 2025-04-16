<?php

namespace App\Http\Controllers;

use App\Services\VisiteurService;
use App\Models\Visiteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $donnees_valides = $request->validate([
            'nom' => 'required|string|max:50',
            'prenom' => 'required|string|max:50',
            'cin' => 'required|string|max:20',
            'email' => 'nullable|string|max:50|email|unique:visiteur,email',
            'telephone' => 'nullable|string|max:10|regex:/^[0-9]+$/',
            'genre' => 'required|string|max:20',
            'entreprise' => 'nullable|string|max:150'
        ]);

        $visiteur = $this->visiteurService->create($donnees_valides);

        return response()->json($visiteur, 201);
    }

    public function update($id, Request $request)
    {
        $donnees_valides = $request->validate([
            'nom' => 'required|string|max:50',
            'prenom' => 'required|string|max:50',
            'cin' => 'required|string|max:20',
            'email' => 'nullable|string|max:50|email|unique:visiteur,email,' . $id,
            'telephone' => 'nullable|string|max:50|regex:/^[0-9]+$/',
            'genre' => 'required|string|max:20',
            'entreprise' => 'nullable|string|max:150'
        ]);

        $visiteur = $this->visiteurService->update($id, $donnees_valides);

        return response()->json($visiteur);
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
