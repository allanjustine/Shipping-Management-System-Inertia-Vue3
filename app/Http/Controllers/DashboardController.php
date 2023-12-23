<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
{
    $user = auth()->user();

    if ($user->hasRole('Admin')) {
        $tickets = Ticket::with('user', 'ship')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        // Admin logic
        $currentMonthTickets = Ticket::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $lastMonthTickets = Ticket::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();

        $percentageChange = $lastMonthTickets != 0
            ? (($currentMonthTickets - $lastMonthTickets) / $lastMonthTickets) * 100
            : 100;

        $cancelledTicketsByMonth = Ticket::where('status', 'Cancelled')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $upcomingTicketsByMonth = Ticket::where('status', 'Pending')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
        $acceptedTicket = Ticket::where('status', 'Accepted')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $totalUsers = User::count();
    } elseif ($user->hasRole('Standard')) {


        $tickets = Ticket::with(['user', 'ship'])
            ->whereHas('user', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })

            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $currentMonthTickets = Ticket::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('user_id', $user->id)
            ->count();

        $lastMonthTickets = Ticket::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->where('user_id', $user->id)
            ->count();

        $percentageChange = $lastMonthTickets != 0
            ? (($currentMonthTickets - $lastMonthTickets) / $lastMonthTickets) * 100
            : 100;

        $cancelledTicketsByMonth = Ticket::where('status', 'Cancelled')
            ->where('user_id', $user->id)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $upcomingTicketsByMonth = Ticket::where('status', 'Pending')
            ->where('user_id', $user->id)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
        $acceptedTicket = Ticket::where('status', 'Accepted')
            ->where('user_id', $user->id)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $totalUsers = User::count();
    } else {
        // Handle other roles or unauthorized access as needed
        return abort(403);
    }

    foreach ($tickets as &$ticket) {
        $patientName = $ticket['user']['name'] ;
        $time = Carbon::parse($ticket['time']);

    if ($time === false) {
        // Handle parsing error
        // For example, log the error or set a default time
        $formattedTime = 'Invalid Time';
    } else {
        $formattedTime = $time->format('h:i A'); // Format to 12-hour time with AM/PM
    }

        $ticket['popoverLabel'] = "{$patientName} - {$formattedTime} - {$ticket['status']}";

        // Additional code to set other properties if needed
    }

    return inertia('Dashboard', [
        'user' => $user,
        'tickets' => $tickets,
        'acceptedTicket' => $acceptedTicket,
        'totalTickets' => $currentMonthTickets,
        'percentageChange' => $percentageChange,
        'totalUsers' => $totalUsers,
        'filters' => request()->only(['search']),
        'cancelledTicketsByMonth' => $cancelledTicketsByMonth,
        'upcomingTicketsByMonth' => $upcomingTicketsByMonth
    ]);
}
}
