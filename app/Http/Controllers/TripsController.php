<?php

namespace App\Http\Controllers;

use App\Models\Trips;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class TripsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trips = Trips::latest()->get();
        return view('trips.index', compact('trips'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicles = Vehicle::all();
        return view('trips.create', compact('vehicles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'Date' => ['nullable', 'date'],
            'vehicle_id' => ['nullable','max:255'],
            'purpose' => ['nullable', 'string', 'max:255'],
        ]);

        Trips::create($data);

        return redirect()->route('trips.index')->with('success', 'Trip created successfully.');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Trips $trips)
    {
        return redirect()->route('trips.edit', $trips);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trips $trip)
    {
        $vehicles = Vehicle::all();
        return view('trips.edit', compact('trip','vehicles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trips $trip)
    {
        $data = $request->validate([
            'Date' => ['nullable', 'date'],
            'vehicle_id' => ['nullable','max:255'],
            'purpose' => ['nullable', 'string', 'max:255'],
        ]);

        $trip->update($data);

        return redirect()->route('trips.index')->with('success', 'Trip updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trips $trip)
    {
        $trip->delete();
        return redirect()->route('trips.index')->with('success', 'Trip deleted successfully.');
    }
}
