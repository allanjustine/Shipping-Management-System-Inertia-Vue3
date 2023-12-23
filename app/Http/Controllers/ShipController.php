<?php

namespace App\Http\Controllers;

use App\Models\Ship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as HttpRequest;


class ShipController extends Controller
{
    public function index()
    {
        $shipCount = Ship::count();
        // $ships = Ship::with('doctors')->get();
        $ships = Ship::query()
            ->when(HttpRequest::input('search'), function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('departure', 'like', '%' . $search . '%')
                ->orWhere('arrival', 'like', '%' . $search . '%')
                ->orWhere('departure_time', 'like', '%' . $search . '%')
                ->orWhere('arrival_time', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhere('ticket_price', 'like', '%' . $search . '%');
            })
            ->paginate(8)
            ->withQueryString();
        return inertia('Ship/Index', [
            'ships' => $ships,
            'shipCount' => $shipCount,
            'filters' => HttpRequest::only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required',
            'departure' => 'required',
            'arrival' => 'required',
            'departure_time' => 'required',
            'arrival_time' => 'required',
            'ticket_price' => 'required',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:10240',
            'description' => 'required',
        ]);

        $fileName = null;
        if ($request->hasFile('image')) {
            $fileName = time() . "." . $request->image->extension();
            $request->image->move(public_path('ships/'), $fileName);
            $fields['image'] = $fileName; // Fix: Use $fields instead of $radiologicData
        }
        $ship =   Ship::create($fields);
        // $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " created a ship - " . $ship->name;
        // event(new UserLog($log_entry));
        return redirect('/ship')->with('success', $ship->name . ' Ship successfully created');
    }

    public function update(Request $request, Ship $ship)
    {
        $fields = $request->validate([
            'name' => 'string',
            'departure' => 'string',
            'arrival' => 'string',
            'departure_time' => 'string',
            'arrival_time' => 'string',
            'ticket_price' => 'string',
            'image' => 'file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:10240',
            'description' => 'string',
        ]);

        $fileName = null;
        if ($request->hasFile('image')) {
            $fileName = time() . "." . $request->image->extension();
            $request->image->move(public_path('ships/'), $fileName);
            $fields['image'] = $fileName; // Fix: Use $fields instead of $radiologicData
        }
        $ship->update($fields);
        // $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " updated a ship - " . $ship->name;
        // event(new UserLog($log_entry));
        return redirect('/ship')->with('success', $ship->name . " Ship successfully updated");
    }

    public function destroy(Ship $ship)
    {
        $ship->delete();
        // $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " deleted a ship - " . $ship->name;
        // event(new UserLog($log_entry));
        return back()->with('success', $ship->arrival . " Ship successfully deleted");
    }
}
