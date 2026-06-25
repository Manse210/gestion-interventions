<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $fillable = ['user_id', 'title', 'message', 'read', 'ticket_ref'];

    protected $casts = [
        'read' => 'boolean',
    ];

    protected $table = 'notifications';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
