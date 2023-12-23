<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Ship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as HttpRequest;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin =  $user->hasAnyRole(['Admin']);

        // $query = Ticket::with('user', 'ship')
        // ->orderBy('id', 'desc');
        // if ($request->filled('from_date') && $request->filled('to_date')) {
        //     $query->whereBetween('requestDate', [$request->from_date, $request->to_date]);
        // }
        // $tickets = $query->paginate(6);
        if ($user->hasRole('Admin')) {
            $tickets = Ticket::query()
                ->with(['user', 'ship'])
                ->orderBy('created_at', 'desc')
                ->when(HttpRequest::input('search'), function ($query, $search) {
                    $query->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    })->orWhereHas('ship', function ($shipQuery) use ($search) {
                        $shipQuery->where('name', 'like', '%' . $search . '%');
                    });
                });

            if (HttpRequest::filled('from_date') && HttpRequest::filled('to_date')) {
                $tickets->whereBetween('date', [HttpRequest::input('from_date'), HttpRequest::input('to_date')]);
            }

            $tickets = $tickets->paginate(8)->withQueryString();
        }
         elseif ($user->hasRole('Standard')) {
            $tickets = Ticket::with(['user', 'ship'])
                ->whereHas('user', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }) ->orderBy('created_at', request('sort', 'desc'))
                ->when(HttpRequest::input('search'), function($query, $search){
                    $query->whereHas('user', function($userQuery) use ($search){
                        $userQuery->where('name',  'like' , '%' . $search . '%');
                    })->orWhereHas('ship', function ($shipQuery) use ($search) {
                        $shipQuery->where('name', 'like', '%' . $search . '%');
                    });
                });
                if (HttpRequest::filled('from_date') && HttpRequest::filled('to_date')) {
                    $tickets->whereBetween('date', [HttpRequest::input('from_date'), HttpRequest::input('to_date')]);
                }

                $tickets = $tickets->paginate(8)->withQueryString();

        }  else {
            // Handle other roles or unauthorized access as needed
            return abort(403);
        }


        return inertia('Ticket/Index', [
            'tickets' => $tickets,

            'filters' => HttpRequest::only(['search']),
            'isAdmin' => $isAdmin
        ]);
    }

    public function create(){
        $user = Auth::user();
        $ships = Ship::all();
        $isAdmin =  $user->hasAnyRole(['Admin']);
        $isStandard =  $user->hasRole('Standard');
        // $users = User::where('type', "Standard")->get();
        $users = User::where('type', 'Standard')->get();

        return inertia('Ticket/Create', [
            'ships' => $ships,
            'isAdmin' => $isAdmin,
            'isStandard'=> $isStandard,
            'users' => $users,
            'user'=> $user
        ]);
    }

    public function store(Request $request){
        $user = Auth::user();

        $fields = $request->validate([
            'ship_id' => 'required|exists:ships,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'ticket_quantity' => 'required',
        ]);

        if ($user->hasRole('Standard')) {
            $fields['user_id'] = $user->id;
        } elseif ($user->hasRole('Admin')) {
            // If the user is an Admin, expect the doc_id from the form
            $fields['user_id'] = $request->input('user_id');
            $fields['status'] = "Accepted";
        }


       $ticket =  Ticket::create($fields);
    //    $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " created an ticket with ". $ticket->patient->firstname. " " . $ticket->patient->lastname. " with the id# ". $ticket->id;
    //    event(new UserLog($log_entry));

        return redirect()->route('ticket.index')->with('success', 'Ticket created successfully.');
    }
    public function calendar(){
        // $tickets = Ticket::with('patient', 'ship', 'doctor.user')->orderBy('date', 'asc')->get();
        $tickets = Ticket::with('user', 'ship')
        ->orderBy('created_at', 'desc')
        ->get();


        return response()->json($tickets);
    }

    public function accept(Ticket $ticket){
        $ticket->update(['status' => 'Accepted']);

        $user = $ticket->user;



        // $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " accepted an ticket with ". $ticket->patient->firstname. " " . $ticket->patient->lastname. " with the id# ". $ticket->id;
        // event(new UserLog($log_entry));

        return redirect()->route('ticket.index')->with('success', 'Ticket approved successfully.');
    }

    public function cancel(Ticket $ticket){
        // Update the ticket status to 'canceled'
        $ticket->update(['status' => 'Cancelled']);

        $user = $ticket->user;



        // $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " cancelled an ticket with ". $ticket->patient->firstname. " " . $ticket->patient->lastname. " with the id# ". $ticket->id;
        // event(new UserLog($log_entry));

        return redirect()->route('ticket.index')->with('success', 'Ticket canceled successfully.');
    }

    public function withship(Ship $ship = null){
        $user = Auth::user();

        $isAdmin =  $user->hasAnyRole(['Admin']);
        $isStandard =  $user->hasRole('Standard');
        $users = User::where('type', 'Standard')->get();

        if ($ship) {
            $ships = [$ship];
        } else {
            $ships = Ship::all();
        }

        return inertia('Ticket/Create2', [
            'ship' => $ships,
            'isAdmin' => $isAdmin,
            'isStandard'=> $isStandard,
            'users' => $users,
            'user'=> $user
        ]);
    }

    public function storeShip(Request $request){
        $user = Auth::user();

        $fields = $request->validate([

            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'ticket_quantity' => 'required',
        ]);

        if ($user->hasRole('Standard')) {
            $fields['user_id'] = $user->id;
        } elseif ($user->hasRole('Admin')) {

            $fields['user_id'] = $request->input('user_id');
            $fields['status'] = "Accepted";
        }


        if ($request->has('ship_id')) {
            $fields['ship_id'] = $request->input('ship_id');
            $ship = Ship::find($fields['ship_id']);
            $successMessage = 'Ship booked successfully.';
        } else {
            // No need to fetch ships again, as it was done in the withship method
            $successMessage = 'All Ships retrieved successfully.';
        }


       $ticket =  Ticket::create($fields);


        return redirect()->route('ticket.index')->with('success', 'Ticket created successfully.');
    }
}

