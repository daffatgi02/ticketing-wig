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

        return view('admin.dashboard', compact(
            'userCounts',
            'ticketCounts',
            'ticketsPerMonth',
            'recentTickets'
        ));
    }
}
