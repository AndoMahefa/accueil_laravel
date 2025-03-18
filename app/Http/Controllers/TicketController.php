<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    protected TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function index()
    {
        $tickets = $this->ticketService->findAll();

        return response()->json($tickets);
    }

    public function show($id)
    {
        $ticket = $this->ticketService->findById($id);

        return response()->json($ticket);
    }

    public function destroy($id)
    {
        $ticket = $this->ticketService->delete($id);

        return response()->json(null, 204);
    }

    // public function ticketsLeJourJ(Request $request)
    // {
    //     $idService = $request->input('id_service');
    //     $today = Carbon::now();

    //     $tickets = Ticket::with('visiteur')
    //         ->whereDate('date', $today)
    //         ->where('id_service', $idService)
    //         ->orderBy('heure_prevu', 'asc')
    //         ->get();

    //     return response()->json($tickets);
    // }

    public function ticketsLeJourJ($idDirection = null) {
        $today = Carbon::now();

        // return $idDirection;

        $query = Ticket::with('visiteur')
            ->with('direction')
            ->with('service')
            ->whereDate('date', $today)
            ->orderBy('heure_prevu', 'asc');

        if ($idDirection) {
            $query->where('id_direction', $idDirection);
            $query->where('id_service', '=', null);
        }

        $tickets = $query->get();

        return response()->json($tickets);
    }

    public function ticketsLeJourJByService($idService = null) {
        $today = Carbon::now();

        // return $idService;

        $query = Ticket::with('visiteur')
            ->with('direction')
            ->with('service')
            ->whereDate('date', $today)
            ->where('id_service', $idService)
            ->orderBy('heure_prevu', 'asc');


        $tickets = $query->get();

        return response()->json($tickets);
    }
}
