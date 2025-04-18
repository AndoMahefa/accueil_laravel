<?php

namespace App\Http\Controllers;

use App\Exports\EmployeExport;
use App\Imports\EmpGlobalImport;
use App\Imports\EmployeImport;
use App\Models\Direction;
use App\Models\Employe;
use App\Models\Fonction;
use App\Models\Observation;
use App\Models\Service;
use App\Services\EmployeService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EmployeController extends Controller
{
    protected EmployeService $employeService;
    protected UserService $userService;
    public function __construct(EmployeService $employeService, UserService $userService)
    {
        $this->employeService = $employeService;
        $this->userService = $userService;
    }

    public function findEmployes(Request $request)
    {
        $search = $request->input('search');

        $query = Employe::query()
            ->with('utilisateur')
            ->with('direction')
            ->with('service')
            ->with('observation')
            ->whereNull('deleted_at');

        if ($search) {
            $query->where('nom', 'ilike', "%$search%")
                ->orWhere('prenom', 'ilike', "%$search%")
                ->orWhere('cin', 'ilike', "%$search%");
        }

        $employes = $query->paginate(10);

        return response()->json([
            'message' => 'liste des employes',
            'employes' => $employes
        ]);
    }

    public function findEmployesByService($idService, Request $request)
    {
        Log::info("id service : " . $idService);
        $search = $request->input('search');

        $query = Employe::query()
            ->with('utilisateur')
            ->with('direction')
            ->with('service')
            ->with('observation')
            ->whereNull('deleted_at')
            ->where('id_service', $idService);

        if ($search) {
            $query->where('nom', 'ilike', "%$search%")
                ->orWhere('prenom', 'ilike', "%$search%")
                ->orWhere('cin', 'ilike', "%$search%");
        }

        $employes = $query->paginate(10);

        return response()->json([
            'message' => 'liste des employes',
            'employes' => $employes
        ]);
    }

    public function findEmployesByDirection($idDirection, Request $request)
    {
        $search = $request->input('search');

        $query = Employe::query()
            ->with('utilisateur')
            ->with('direction')
            ->with('service')
            ->with('observation')
            ->whereNull('deleted_at')
            ->where('id_direction', $idDirection);

        if ($search) {
            $query->where('nom', 'ilike', "%$search%")
                ->orWhere('prenom', 'ilike', "%$search%")
                ->orWhere('cin', 'ilike', "%$search%");
        }

        $employes = $query->paginate(10);

        return response()->json([
            'message' => 'liste des employes',
            'employes' => $employes
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string',
                'prenom' => 'required|string',
                'date_de_naissance' => 'required|date',
                'adresse' => 'required|string|max:75',
                'cin' => 'required|string|max:25|unique:employe,cin',
                'telephone' => 'required|string|max:25|unique:employe,telephone',
                'genre' => 'required|string|max:20',
                'id_fonction' => 'required|int|exists:fonction,id',
                'id_direction' => 'required|int|exists:direction,id',
                'id_observation' => 'required|int|exists:observation,id',
                'id_service' => 'nullable|int|exists:service,id'
            ], [
                'nom.required' => 'Le nom est requis.',
                'nom.string' => 'Le nom doit être une chaîne de caractères.',
                'prenom.required' => 'Le prénom est requis.',
                'prenom.string' => 'Le prénom doit être une chaîne de caractères.',
                'date_de_naissance.required' => 'La date de naissance est requise.',
                'date_de_naissance.date' => 'La date de naissance doit être une date valide.',
                'adresse.required' => 'L\'adresse est requise.',
                'adresse.string' => 'L\'adresse doit être une chaîne de caractères.',
                'adresse.max' => 'L\'adresse ne doit pas dépasser 75 caractères.',
                'cin.required' => 'Le CIN est requis.',
                'cin.unique' => 'Le CIN doit être unique.',
                'telephone.required' => 'Le téléphone est requis.',
                'telephone.unique' => 'Le téléphone doit être unique.',
                'genre.required' => 'Le genre est requis.',
                'id_fonction.required' => 'La fonction est requise.',
                'id_fonction.exists' => 'La fonction sélectionnée n\'existe pas.',
                'id_direction.required' => 'La direction est requise.',
                'id_direction.exists' => 'La direction sélectionnée n\'existe pas.',
                'id_observation.required' => 'L\'observation est requise.',
                'id_observation.exists' => 'L\'observation sélectionnée n\'existe pas.',
                'id_service.exists' => 'Le service sélectionné n\'existe pas.',
            ]);

            $employe = $this->employeService->create($validated);

            return response()->json([
                'message' => 'Employé créé avec succès',
                'employe' => $employe
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation, veuillez vérifier les données fournies.',
                'errors' => $e->errors()
            ]);
        }
    }

    public function update($id, Request $request)
    {
        try {
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
                'id_service' => 'nullable|exists:service,id'
            ], [
                'nom.required' => 'Le nom est requis.',
                'nom.string' => 'Le nom doit être une chaîne de caractères.',
                'prenom.required' => 'Le prénom est requis.',
                'prenom.string' => 'Le prénom doit être une chaîne de caractères.',
                'date_de_naissance.required' => 'La date de naissance est requise.',
                'date_de_naissance.date' => 'La date de naissance doit être une date valide.',
                'adresse.required' => 'L\'adresse est requise.',
                'adresse.string' => 'L\'adresse doit être une chaîne de caractères.',
                'adresse.max' => 'L\'adresse ne doit pas dépasser 75 caractères.',
                'cin.required' => 'Le CIN est requis.',
                'cin.unique' => 'Le CIN doit être unique.',
                'telephone.required' => 'Le téléphone est requis.',
                'telephone.unique' => 'Le téléphone doit être unique.',
                'genre.required' => 'Le genre est requis.',
                'id_fonction.required' => 'La fonction est requise.',
                'id_fonction.exists' => 'La fonction sélectionnée n\'existe pas.',
                'id_direction.required' => 'La direction est requise.',
                'id_direction.exists' => 'La direction sélectionnée n\'existe pas.',
                'id_observation.required' => 'L\'observation est requise.',
                'id_observation.exists' => 'L\'observation sélectionnée n\'existe pas.',
                'id_service.exists' => 'Le service sélectionné n\'existe pas.',
            ]);

            $emp = $this->employeService->update($id, $validated);
            return response()->json([
                'message' => 'Employé mis a jour avec succes',
                'employe' => $emp
            ], 204);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation, veuillez vérifier les données fournies.',
                'errors' => $e->errors()
            ]);
        }
    }

    public function destroy($id)
    {
        $this->employeService->delete($id);

        return response()->json(['message' => 'Employe supprime avec succes'], 204);
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

    public function createUserForEmploye(Request $request)
    {
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

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new EmpGlobalImport, $request->file('file'));
        return response()->json(['message' => 'Importation réussie']);
    }

    public function exportTemplate()
    {
        $spreadsheet = new Spreadsheet();

        // Feuille principale (Bordereau)
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Employe');

        // En-têtes du fichier
        $sheet->setCellValue('A1', 'Nom');
        $sheet->setCellValue('B1', 'Prenom');
        $sheet->setCellValue('C1', 'Date de naissance');
        $sheet->setCellValue('D1', 'Adresse');
        $sheet->setCellValue('E1', 'CIN');
        $sheet->setCellValue('F1', 'Téléphone');
        $sheet->setCellValue('G1', 'Genre');
        $sheet->setCellValue('H1', 'Direction');
        $sheet->setCellValue('I1', 'Service');
        $sheet->setCellValue('J1', 'Fonction');
        $sheet->setCellValue('K1', 'observation');

        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(30);

        $this->directionSheet($spreadsheet);
        $this->serviceSheet($spreadsheet);
        $this->fonctionSheet($spreadsheet);
        $this->observationSheet($spreadsheet);

        // Revenir à la première feuille
        $spreadsheet->setActiveSheetIndex(0);


        // Sauvegarde du fichier temporaire
        $writer = new Xlsx($spreadsheet);
        $fileName = 'employe_template.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return Response::download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function directionSheet($spreadsheet)
    {
        $spreadsheet->createSheet();
        $refSheet = $spreadsheet->setActiveSheetIndex(1);
        $refSheet->setTitle('Directions');

        $refSheet->setCellValue('A1', 'Direction');

        $directions = Direction::all();
        $row = 2;
        foreach ($directions as $direction) {
            $refSheet->setCellValue('A' . $row, $direction->nom);
            $row++;
        }

        // Ajuster la largeur de la colonne pour voir les références
        $refSheet->getColumnDimension('A')->setAutoSize(true);

        // Utiliser toute la colonne A comme référence
        // $lastDataRow = $row - 1; // Dernière ligne avec des données
        // $range = 'Directions!$A$2:$A$' . $lastDataRow;
        $range = 'Directions!$A$2:$A$1048576';


        // Appliquer la validation de données sur la colonne A de "Bordereau"
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        for ($i = 2; $i <= 100; $i++) {
            $validation = $sheet->getCell("H$i")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowDropDown(true);
            $validation->setFormula1($range);
        }
    }

    public function serviceSheet($spreadsheet)
    {
        $spreadsheet->createSheet();
        $refSheet = $spreadsheet->setActiveSheetIndex(2);
        $refSheet->setTitle('Services');

        // En-têtes
        $refSheet->setCellValue('A1', 'Service');
        $refSheet->setCellValue('B1', 'Direction');

        $services = Service::with('direction')->get();
        $row = 2;
        foreach ($services as $service) {
            $refSheet->setCellValue('A' . $row, $service->nom);
            $refSheet->setCellValue('B' . $row, $service->direction->nom);
            $row++;
        }

        // Ajuster la largeur de la colonne pour voir les références
        $refSheet->getColumnDimension('A')->setAutoSize(true);
        $refSheet->getColumnDimension('B')->setAutoSize(true);

        // Utiliser toute la colonne A comme référence
        // $range = 'Services!$A:$A';
        $range = 'Services!$A$2:$A$1048576';
        // $lastDataRow = $row - 1; // Dernière ligne avec des données
        // $range = 'Services!$A$2:$A$' . $lastDataRow;

        // Appliquer la validation de données sur la colonne A de "Bordereau"
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        for ($i = 2; $i <= 100; $i++) {
            $validation = $sheet->getCell("I$i")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowDropDown(true);
            $validation->setFormula1($range);
        }
    }

    public function fonctionSheet($spreadsheet)
    {
        $spreadsheet->createSheet();
        $refSheet = $spreadsheet->setActiveSheetIndex(3);
        $refSheet->setTitle('Fonctions');

        // En-têtes
        $refSheet->setCellValue('A1', 'Fonction');
        $refSheet->setCellValue('B1', 'Direction');
        $refSheet->setCellValue('C1', 'Service');


        $fonctions = Fonction::with('direction', 'service')->get();
        $row = 2;
        foreach ($fonctions as $fonction) {
            $refSheet->setCellValue('A' . $row, $fonction->nom);
            $refSheet->setCellValue('B' . $row, $fonction->direction->nom);
            $refSheet->setCellValue('C' . $row, $fonction->service ? $fonction->service->nom : '');
            $row++;
        }

        // Ajuster la largeur de la colonne pour voir les références
        $refSheet->getColumnDimension('A')->setAutoSize(true);
        $refSheet->getColumnDimension('B')->setAutoSize(true);
        $refSheet->getColumnDimension('C')->setAutoSize(true);

        // Utiliser toute la colonne A comme référence
        // $range = 'Fonctions!$A:$A';
        $range = 'Fonctions!$A$2:$A$1048576';
        // $lastDataRow = $row - 1; // Dernière ligne avec des données
        // $range = 'Fonctions!$A$2:$A$' . $lastDataRow;

        // Appliquer la validation de données sur la colonne A de "Bordereau"
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        for ($i = 2; $i <= 100; $i++) {
            $validation = $sheet->getCell("J$i")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowDropDown(true);
            $validation->setFormula1($range);
        }
    }

    public function observationSheet($spreadsheet)
    {
        $spreadsheet->createSheet();
        $refSheet = $spreadsheet->setActiveSheetIndex(4);
        $refSheet->setTitle('Observations');

        $refSheet->setCellValue('A1', 'Observation');

        $observations = Observation::all();
        $row = 2;
        foreach ($observations as $observation) {
            $refSheet->setCellValue('A' . $row, $observation->observation);
            $row++;
        }

        // Ajuster la largeur de la colonne pour voir les références
        $refSheet->getColumnDimension('A')->setAutoSize(true);

        // Utiliser toute la colonne A comme référence
        $range = 'Observations!$A$2:$A$1048576';
        // $lastDataRow = $row - 1; // Dernière ligne avec des données
        // $range = 'Observations!$A$2:$A$' . $lastDataRow;
        // $range = 'Observations!$A:$A';

        // Appliquer la validation de données sur la colonne A de "Bordereau"
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        for ($i = 2; $i <= 100; $i++) {
            $validation = $sheet->getCell("K$i")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowDropDown(true);
            $validation->setFormula1($range);
        }
    }

    public function export(Request $request)
    {
        $directionId = $request->query('direction_id');
        $serviceId = $request->query('service_id');

        return Excel::download(
            new EmployeExport($directionId, $serviceId),
            'employes.xlsx'
        );
    }
}
