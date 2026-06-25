<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Ticket;
use App\Models\TicketHistory;
use App\Models\AppNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'travaux' => 'required|string',
            'duree_heures' => 'required|integer|min:0',
            'duree_minutes' => 'required|integer|min:0|max:59',
            'materiel' => 'nullable|string',
            'observations' => 'nullable|string',
            'recommandations' => 'nullable|string',
        ]);

        $data['signed_by'] = $request->user()->name;
        $data['ticket_id'] = $ticket->id;

        Report::create($data);
        $ticket->update(['statut' => 'Resolu']);

        TicketHistory::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'action' => "Rapport soumis par {$request->user()->name} — Statut passé à \"Résolu\"",
        ]);

        AppNotification::create([
            'user_id' => $ticket->client_id,
            'title' => 'Rapport disponible',
            'message' => "Le rapport pour le ticket {$ticket->ref} est disponible.",
            'ticket_ref' => $ticket->ref,
        ]);

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'Rapport soumis avec succès.');
    }

    public function download(Ticket $ticket)
    {
        $report = $ticket->report;
        if (!$report) {
            return back()->with('error', 'Aucun rapport pour ce ticket.');
        }

        $ticket->load('client', 'technicien');
        $pdf = Pdf::loadView('reports.pdf', [
            'ticket' => $ticket,
            'report' => $report,
        ]);

        return $pdf->download("Rapport_{$ticket->ref}.pdf");
    }
}
