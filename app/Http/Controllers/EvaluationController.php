<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Ticket;
use App\Models\TicketHistory;
use App\Models\AppNotification;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:500',
        ]);

        Evaluation::create([
            'ticket_id' => $ticket->id,
            'client_id' => $request->user()->id,
            'note' => $data['note'],
            'commentaire' => $data['commentaire'],
        ]);

        $ticket->update(['statut' => 'Ferme']);

        TicketHistory::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'action' => "Évaluation soumise ({$data['note']}/5) — Ticket fermé",
        ]);

        AppNotification::create([
            'user_id' => 1,
            'title' => 'Nouvelle évaluation',
            'message' => "Le ticket {$ticket->ref} a reçu une note de {$data['note']}/5.",
            'ticket_ref' => $ticket->ref,
        ]);

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'Merci pour votre évaluation !');
    }
}
