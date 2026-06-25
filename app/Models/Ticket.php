<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'ref', 'titre', 'description', 'priorite', 'categorie',
        'statut', 'client_id', 'technicien_id', 'piece_jointe', 'deadline',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function technicien()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }

    public function history()
    {
        return $this->hasMany(TicketHistory::class);
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function report()
    {
        return $this->hasOne(Report::class);
    }

    public function evaluation()
    {
        return $this->hasOne(Evaluation::class);
    }
}
