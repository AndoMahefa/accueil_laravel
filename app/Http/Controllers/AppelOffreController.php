<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Services\AppelOffreService;

class AppelOffreController extends Controller
{
    protected $appelOffreService;
    public function __construct(AppelOffreService $appelOffreService) {
        $this->appelOffreService = $appelOffreService;
    }

    public function index() {
        return response()->json($this->appelOffreService->findAll());
    }

    public function show($id) {
        return response()->json($this->appelOffreService->findById($id));
    }

    public function store(Request $request) {
        return response()->json($this->appelOffreService->create($request->all()));
    }
}
