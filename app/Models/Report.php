<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'ticket_id', 'travaux', 'duree_heures', 'duree_minutes',
        'materiel', 'observations', 'recommandations', 'signed_by',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
