<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Evaluation;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function tickets()
    {
        return Inertia::render('Admin/Tickets', [
            'tickets' => Ticket::with('client', 'technicien', 'history.user')->latest()->get(),
            'techniciens' => User::where('role', 'technicien')->where('actif', true)->get(),
        ]);
    }

    public function users()
    {
        return Inertia::render('Admin/Users', [
            'users' => User::latest()->get(),
        ]);
    }

    public function storeTechnician(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'specialite' => 'required|string',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'technicien',
            'specialite' => $data['specialite'],
        ]);

        return back()->with('success', 'Technicien créé avec succès.');
    }

    public function toggleUser(User $user)
    {
        $user->update(['actif' => !$user->actif]);
        $status = $user->actif ? 'activé' : 'désactivé';

        AppNotification::create([
            'user_id' => $user->id,
            'title' => 'Compte ' . $status,
            'message' => "Votre compte a été {$status} par l'administrateur.",
        ]);

        return back()->with('success', "Utilisateur {$status}.");
    }

    public function reviews()
    {
        return Inertia::render('Admin/Reviews', [
            'evaluations' => Evaluation::with('ticket', 'client')->latest()->get(),
            'techniciens' => User::where('role', 'technicien')->get(),
        ]);
    }
}
