<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $period = $request->query('period', 'Mois');

        return match ($user->role) {
            'admin' => $this->adminDashboard($period),
            'technicien' => $this->techDashboard(),
            default => $this->clientDashboard(),
        };
    }

    private function getPeriodDays(string $period): int
    {
        return match ($period) {
            'Semaine' => 7,
            'Trimestre' => 90,
            default => 30,
        };
    }

    private function getLineLabels(string $period): array
    {
        $days = $this->getPeriodDays($period);
        $labels = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i);
            $labels[] = $period === 'Semaine'
                ? $d->translatedFormat('D d')
                : ($period === 'Trimestre' ? $d->translatedFormat('d M') : $d->translatedFormat('d M'));
        }
        return $labels;
    }

    private function adminDashboard(string $period)
    {
        $days = $this->getPeriodDays($period);
        $since = Carbon::now()->subDays($days);

        $ticketsQuery = Ticket::where('created_at', '>=', $since);

        $allTickets = (clone $ticketsQuery)->get();

        $lineData = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->toDateString();
            $lineData[] = $allTickets->filter(fn($t) => Carbon::parse($t->created_at)->toDateString() === $date)->count();
        }

        return Inertia::render('Admin/Dashboard', [
            'period' => $period,
            'periodLabel' => $this->getLineLabels($period),
            'lineChartData' => $lineData,
            'stats' => [
                'total' => (clone $ticketsQuery)->count(),
                'ouvert' => (clone $ticketsQuery)->where('statut', 'Ouvert')->count(),
                'en_cours' => (clone $ticketsQuery)->where('statut', 'En cours')->count(),
                'resolu' => (clone $ticketsQuery)->where('statut', 'Resolu')->count(),
                'ferme' => (clone $ticketsQuery)->where('statut', 'Ferme')->count(),
            ],
            'ticketsParPriorite' => (clone $ticketsQuery)->selectRaw('priorite, count(*) as total')->groupBy('priorite')->get(),
            'ticketsParCategorie' => (clone $ticketsQuery)->selectRaw('categorie, count(*) as total')->groupBy('categorie')->get(),
            'performanceTechniciens' => User::where('role', 'technicien')->withCount(['ticketsAsTechnician' => fn($q) => $q->where('created_at', '>=', $since)])->get()->map(fn($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'specialite' => $t->specialite,
                'total' => $t->tickets_as_technician_count,
                'moyenne' => Evaluation::whereHas('ticket', fn($q) => $q->where('technicien_id', $t->id)->where('created_at', '>=', $since))->avg('note') ?? 0,
            ]),
            'ticketsRecents' => (clone $ticketsQuery)->with('client')->latest()->take(10)->get(),
        ]);
    }

    private function techDashboard()
    {
        return Inertia::render('Tech/Dashboard', [
            'stats' => [
                'assignes' => Ticket::where('technicien_id', auth()->id())->count(),
                'en_cours' => Ticket::where('technicien_id', auth()->id())->where('statut', 'En cours')->count(),
                'resolus' => Ticket::where('technicien_id', auth()->id())->whereIn('statut', ['Resolu', 'Ferme'])->count(),
            ],
            'tickets' => Ticket::with('client')
                ->where('technicien_id', auth()->id())
                ->whereNotIn('statut', ['Resolu', 'Ferme'])
                ->latest()
                ->get(),
        ]);
    }

    private function clientDashboard()
    {
        return Inertia::render('Client/Dashboard', [
            'stats' => [
                'total' => Ticket::where('client_id', auth()->id())->count(),
                'ouverts' => Ticket::where('client_id', auth()->id())->where('statut', 'Ouvert')->count(),
                'en_cours' => Ticket::where('client_id', auth()->id())->where('statut', 'En cours')->count(),
                'resolus' => Ticket::where('client_id', auth()->id())->whereIn('statut', ['Resolu', 'Ferme'])->count(),
            ],
            'tickets' => Ticket::with('technicien')
                ->where('client_id', auth()->id())
                ->latest()
                ->get(),
        ]);
    }
}
