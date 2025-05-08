<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            // Admin sees all tickets
            $openTickets = Ticket::where('status', 'open')->count();
            $inProgressTickets = Ticket::where('status', 'in_progress')->count();
            $resolvedTickets = Ticket::where('status', 'resolved')->count();
            $externalSupportTickets = Ticket::where('needs_external_support', true)->count();

            $recentTickets = Ticket::with(['user', 'category', 'assignedTo'])
                ->latest()
                ->take(5)
                ->get();

            // Get top departments with most tickets
            $departmentTickets = Ticket::select('departments.name', DB::raw('count(*) as count'))
                ->join('users', 'tickets.user_id', '=', 'users.id')
                ->join('departments', 'users.department_id', '=', 'departments.id')
                ->groupBy('departments.name')
                ->orderBy('count', 'desc')
                ->take(5)
                ->get();

            // Get top users who create most tickets
            $topReporters = Ticket::select('users.name', 'departments.name as department', DB::raw('count(*) as count'))
                ->join('users', 'tickets.user_id', '=', 'users.id')
                ->join('departments', 'users.department_id', '=', 'departments.id')
                ->groupBy('users.name', 'departments.name')
                ->orderBy('count', 'desc')
                ->take(5)
                ->get();

            return view('home', compact(
                'openTickets',
                'inProgressTickets',
                'resolvedTickets',
                'externalSupportTickets',
                'recentTickets',
                'departmentTickets',
                'topReporters'
            ));
        } elseif ($user->isSupport()) {
            // Support staff only sees tickets assigned to them
            $openTickets = Ticket::where('status', 'assigned')
                ->where('assigned_to', $user->id)
                ->count();
            $inProgressTickets = Ticket::where('status', 'in_progress')
                ->where('assigned_to', $user->id)
                ->count();
            $resolvedTickets = Ticket::where('status', 'resolved')
                ->where('assigned_to', $user->id)
                ->count();
            $externalSupportTickets = Ticket::where('needs_external_support', true)
                ->where('assigned_to', $user->id)
                ->count();

            $recentTickets = Ticket::where('assigned_to', $user->id)
                ->with(['user', 'category'])
                ->latest()
                ->take(5)
                ->get();

            return view('home', compact(
                'openTickets',
                'inProgressTickets',
                'resolvedTickets',
                'externalSupportTickets',
                'recentTickets'
            ));
        } else {
            // Regular users see their own tickets
            $openTickets = Ticket::where('user_id', $user->id)
                ->whereIn('status', ['open', 'assigned', 'in_progress'])
                ->count();
            $inProgressTickets = Ticket::where('user_id', $user->id)
                ->where('status', 'in_progress')
                ->count();
            $resolvedTickets = Ticket::where('user_id', $user->id)
                ->whereIn('status', ['resolved', 'closed'])
                ->count();
            $externalSupportTickets = Ticket::where('user_id', $user->id)
                ->where('needs_external_support', true)
                ->count();

            $recentTickets = Ticket::where('user_id', $user->id)
                ->with(['category', 'assignedTo'])
                ->latest()
                ->take(5)
                ->get();

            return view('home', compact(
                'openTickets',
                'inProgressTickets',
                'resolvedTickets',
                'externalSupportTickets',
                'recentTickets'
            ));
        }
    }
}
