<?php

namespace App\Http\Controllers;

use App\Models\ReferencePpm;
use Illuminate\Http\Request;

class ReferencePpmController extends Controller {
    public function index() {
        return response()->json(ReferencePpm::all());
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'reference' => 'required|string|max:25'
        ]);

        $reference = ReferencePpm::create($validated);
        return response()->json([
            "message" => "reference creer avec succes",
            "reference" => $reference
        ], 201);
    }
}
