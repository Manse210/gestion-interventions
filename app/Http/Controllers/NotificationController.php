<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return AppNotification::where('user_id', auth()->id())
            ->latest()
            ->get();
    }

    public function markRead(AppNotification $notification)
    {
        $notification->update(['read' => true]);
        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        AppNotification::where('user_id', auth()->id())->update(['read' => true]);
        return response()->json(['success' => true]);
    }

    public function count()
    {
        return AppNotification::where('user_id', auth()->id())
            ->where('read', false)
            ->count();
    }
}
