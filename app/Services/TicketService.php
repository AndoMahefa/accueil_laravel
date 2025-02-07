<?php

namespace App\Services;

use App\Models\Ticket;

class TicketService
{

    public function create(array $data)
    {
        return Ticket::create($data);
    }

    public function findAll()
    {
        return Ticket::all();
    }

    public function findById($id)
    {
        return Ticket::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $ticket = $this->findById($id);
        $ticket->update($data);
        return $ticket;
    }

    public function delete($id)
    {
        $ticket = $this->findById($id);
        $ticket->delete();

        return $ticket;
    }

    public function getLastTicketForService($idDirection)
    {
        return Ticket::where('id_direction', $idDirection)
            ->orderBy('heure_prevu', 'desc') // Trier par heure prévue décroissante
            ->first(); // Récupérer le dernier ticket
    }
}
