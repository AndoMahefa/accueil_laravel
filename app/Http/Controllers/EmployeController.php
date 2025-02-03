<?php
namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\RoleEmploye;
use App\Services\EmployeService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeController extends Controller {
    protected EmployeService $employeService;
    protected UserService $userService;
    public function __construct(EmployeService $employeService, UserService $userService) {
        $this->employeService = $employeService;
        $this->userService = $userService;
    }

    public function findAllByService($idService) {
        $employes = Employe::where('id_service', $idService)
            ->with('roles')
            ->with('utilisateur')
            ->whereNull('deleted_at')
            ->get();

        return response()->json([
            'message' => 'liste de tous les employes',
            'employes' => $employes
        ]);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_de_naissance' => 'required|date',
            'adresse' => 'required|string|max:75',
            'cin' => 'required|string|max:25|unique:employe,cin',
            'telephone' => 'required|string|max:25|unique:employe,telephone',
            'genre' => 'required|string|max:20',
            'id_fonction' => 'required|int|exists:fonction,id',
            'id_direction' => 'required|int|exists:direction,id',
            'id_observation' => 'required|int|exists:observation,id',
            'id_service' => 'nullable|int|exists:service,id'
        ]);

        $employe = $this->employeService->create($validated);

        return response()->json([
            'message' => 'Employé créé avec succès',
            'employe' => $employe
        ], 201);
    }

    public function update($id, Request $request) {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_de_naissance' => 'required|date',
            'adresse' => 'required|string|max:75',
            'cin' => 'required|string|max:25|unique:employe,cin,' . $id,
            'telephone' => 'required|string|max:25|unique:employe,telephone,' . $id,
            'genre' => 'required|string|max:20',
            'id_fonction' => 'required|exists:fonction,id',
            'id_direction' => 'required|exists:direction,id',
            'id_observation' => 'required|exists:observation,id',
            'id_service' => 'required|exists:service,id'
        ]);

        $emp = $this->employeService->update($id, $validated);
        return response()->json([
            'message' => 'employe mis a jour avec succes',
            'employe' => $emp
        ]);
    }

    public function destroy($id) {
        $this->employeService->delete($id);

        return response()->json(['message'=>'Employe supprime avec succes'], 204);
    }

    public function getDeletedEmployes()
    {
        $deletedEmployes = Employe::onlyTrashed()
            ->with('service')
            ->paginate(10);

        return response()->json([
            'message' => 'Employes supprimés récupérés avec succès',
            'employes' => $deletedEmployes
        ]);
    }

    public function restore($id)
    {
        $service = Employe::withTrashed()->find($id);

        if (!$service) {
            return response()->json(['message' => 'Employe introuvable'], 404);
        }

        if ($service->trashed()) {
            $service->restore();
            return response()->json(['message' => 'Employe restauré avec succès']);
        }

        return response()->json(['message' => 'Cet employe n\'était pas supprimé']);
    }

    public function createUserForEmploye(Request $request) {
        $validated = $request->validate([
            'email' => 'required|email|unique:utilisateur,email',
            'mot_de_passe' => 'required|string|min:8',
            'id_employe' => 'required|int|exists:employe,id'
        ]);

        $validated['role'] = 'user';
        $validated['mot_de_passe'] = Hash::make($validated['mot_de_passe']);
        $user = $this->userService->create($validated);

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'utilisateur' => $user
        ], 201);
    }

    public function assignRolesToEmployee(Request $request) {
        // Valider les données de la requête
        $validatedData = $request->validate([
            'id_employe' => 'required|exists:employe,id',
            'id_roles' => 'required|array|min:1',
            'id_roles.*' => 'exists:role_service,id',
        ]);

        $idEmploye = $validatedData['id_employe'];
        $roles = $validatedData['id_roles'];

        // Liste des rôles déjà attribués
        $existingRoles = RoleEmploye::where('id_employe', $idEmploye)
            ->whereIn('id_role', $roles)
            ->pluck('id_role')
            ->toArray();

        // Identifier les nouveaux rôles à ajouter
        $newRoles = array_diff($roles, $existingRoles);

        // Ajouter les nouveaux rôles
        $roleEntries = [];
        foreach ($newRoles as $idRole) {
            $roleEntries[] = [
                'id_employe' => $idEmploye,
                'id_role' => $idRole,
            ];
        }

        // Insérer les nouveaux rôles dans la base de données
        RoleEmploye::insert($roleEntries);

        return response()->json([
            'message' => 'Rôles attribués avec succès.',
            'added_roles' => $newRoles,
            'existing_roles' => $existingRoles,
        ], 201);
    }

    public function deleteRoleEmploye($idEmploye, $idRole) {
        RoleEmploye::where('id_employe', $idEmploye)
            ->where('id_role', $idRole)
            ->delete();

        return response()->json([
            'message' => 'Role supprimé avec succès',
        ], 204);
    }

    public function getRolesByEmploye($idEmploye) {
        $roles = RoleEmploye::where('id_employe', $idEmploye)
            ->with('roleService')
            ->get();

        return response()->json($roles);
    }
}
