<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Count total users by role
        $userCounts = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        // Count tickets by status
        $ticketCounts = Ticket::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Count tickets that need external support
        $externalSupportTickets = Ticket::where('needs_external_support', true)->count();

        // Get tickets created per month for the current year
        $ticketsPerMonth = Ticket::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('count(*) as count')
        )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Recent tickets
        $recentTickets = Ticket::with(['user', 'category', 'assignedTo'])
            ->latest()
            ->take(10)
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

        return view('admin.dashboard', compact(
            'userCounts',
            'ticketCounts',
            'externalSupportTickets',
            'ticketsPerMonth',
            'recentTickets',
            'departmentTickets',
            'topReporters'
        ));
    }
}
