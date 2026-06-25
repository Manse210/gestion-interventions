<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\User;
use Inertia\Inertia;

class TechProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $evaluations = Evaluation::whereHas('ticket', fn($q) => $q->where('technicien_id', $user->id))
            ->with('client')
            ->latest()
            ->get();

        $moyenne = $evaluations->avg('note') ?? 0;
        $totalEvals = $evaluations->count();

        return Inertia::render('Tech/Profile', [
            'evaluations' => $evaluations,
            'moyenne' => round($moyenne, 1),
            'totalEvals' => $totalEvals,
        ]);
    }
}
