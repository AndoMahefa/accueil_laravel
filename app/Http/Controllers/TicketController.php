<?php

namespace App\Http\Controllers;

use App\Services\TicketService;

class TicketController extends Controller {
    protected TicketService $ticketService;

    public function __construct(TicketService $ticketService) {
        $this->ticketService = $ticketService;
    }

    public function index() {
        $tickets = $this->ticketService->findAll();

        return response()->json($tickets);
    }

    public function show($id) {
        $ticket = $this->ticketService->findById($id);

        return response()->json($ticket);
    }

    public function destroy($id){
        $ticket = $this->ticketService->delete($id);

        return response()->json(null, 204);
    }
}
