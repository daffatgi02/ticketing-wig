<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        // Get ticket statistics based on user role
        if ($user->isAdmin()) {
            $openTickets = Ticket::where('status', 'open')->count();
            $inProgressTickets = Ticket::where('status', 'in_progress')->count();
            $resolvedTickets = Ticket::where('status', 'resolved')->count();
            $recentTickets = Ticket::with(['user', 'category', 'assignedTo'])
                ->latest()
                ->take(5)
                ->get();
        } elseif ($user->isSupport()) {
            $openTickets = Ticket::where('status', 'open')->count();
            $inProgressTickets = Ticket::where('status', 'in_progress')
                ->where('assigned_to', $user->id)
                ->count();
            $resolvedTickets = Ticket::where('status', 'resolved')
                ->where('assigned_to', $user->id)
                ->count();
            $recentTickets = Ticket::where('assigned_to', $user->id)
                ->with(['user', 'category'])
                ->latest()
                ->take(5)
                ->get();
        } else {
            $openTickets = Ticket::where('user_id', $user->id)
                ->whereIn('status', ['open', 'assigned', 'in_progress'])
                ->count();
            $inProgressTickets = Ticket::where('user_id', $user->id)
                ->where('status', 'in_progress')
                ->count();
            $resolvedTickets = Ticket::where('user_id', $user->id)
                ->whereIn('status', ['resolved', 'closed'])
                ->count();
            $recentTickets = Ticket::where('user_id', $user->id)
                ->with(['category', 'assignedTo'])
                ->latest()
                ->take(5)
                ->get();
        }

        return view('home', compact('openTickets', 'inProgressTickets', 'resolvedTickets', 'recentTickets'));
    }
}
