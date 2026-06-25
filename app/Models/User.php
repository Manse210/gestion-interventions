<?php

namespace App\Models;

use App\Notifications\TicketNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'specialite', 'actif', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'actif' => 'boolean',
        ];
    }

    public function ticketsAsClient()
    {
        return $this->hasMany(Ticket::class, 'client_id');
    }

    public function ticketsAsTechnician()
    {
        return $this->hasMany(Ticket::class, 'technicien_id');
    }

    public function history()
    {
        return $this->hasMany(TicketHistory::class);
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'client_id');
    }

    public function notifications()
    {
        return $this->hasMany(AppNotification::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTechnicien(): bool
    {
        return $this->role === 'technicien';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }
}
