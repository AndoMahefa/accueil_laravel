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

    public function ticketsLeJourJ(Request $request)
    {
        $idService = $request->input('id_service');
        $today = Carbon::now();

        $tickets = Ticket::with('visiteur')
            ->whereDate('date', $today)
            ->where('id_service', $idService)
            ->orderBy('temps_estime', 'asc')
            ->get();

        return response()->json($tickets);
    }
}
