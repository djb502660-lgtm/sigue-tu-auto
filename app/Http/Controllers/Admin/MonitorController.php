<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminEvent;
use App\Models\User;
use Illuminate\View\View;

class MonitorController extends Controller
{
    public function dashboard(): View
    {
        $stats = [
            'users' => User::count(),
            'admins' => User::where('role', User::ROLE_ADMIN)->count(),
            'maintenance' => User::where('role', User::ROLE_MAINTENANCE)->count(),
            'users_role' => User::where('role', User::ROLE_USER)->count(),
            'events_today' => AdminEvent::whereDate('created_at', now()->toDateString())->count(),
        ];

        $recentEvents = AdminEvent::with('actor')
            ->latest()
            ->take(12)
            ->get();

        return view('admin.monitor.dashboard', compact('stats', 'recentEvents'));
    }

    public function configuration(): View
    {
        return view('admin.monitor.configuration');
    }

    public function account(): View
    {
        return view('admin.monitor.account');
    }

    public function roleAssignment(): View
    {
        $users = User::orderBy('name')->paginate(20);

        return view('admin.monitor.role-assignment', compact('users'));
    }

    public function history(): View
    {
        $events = AdminEvent::with('actor')
            ->latest()
            ->paginate(30);

        return view('admin.monitor.history', compact('events'));
    }

    public function notifications(): View
    {
        $notifications = AdminEvent::with('actor')
            ->latest()
            ->whereIn('category', ['orders', 'roles', 'user_queries', 'auth'])
            ->paginate(30);

        return view('admin.monitor.notifications', compact('notifications'));
    }
}
