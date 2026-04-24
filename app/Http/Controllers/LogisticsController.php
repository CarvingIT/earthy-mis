<?php

namespace App\Http\Controllers;

use App\Models\Logistics;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class LogisticsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $logistics = Logistics::latest()->get();
        return view('logistics.index', compact('logistics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicles = Vehicle::all();
        return view('logistics.create', compact('vehicles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => ['nullable','max:255'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date'],
            'running_kms' => ['nullable', 'string', 'max:255'],
        ]);

        Logistics::create($data);

        return redirect()->route('logistics.index')->with('success', 'Logistics created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Logistics $logistics)
    {
        return redirect()->route('logistics.edit', $logistic);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Logistics $logistic)
    {
        $vehicles = Vehicle::all();
        return view('logistics.edit', compact('logistic','vehicles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Logistics $logistic)
    {
        $data = $request->validate([
            'vehicle_id' => ['nullable', 'max:255'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date'],
            'running_kms' => ['nullable', 'string', 'max:255'],
        ]);

        $logistic->update($data);
        return redirect()->route('logistics.index')->with('success', 'Logistic updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Logistics $logistic)
    {
        $logistic->delete();
        return redirect()->route('logistics.index')->with('success', 'Logistic deleted successfully.');        
    }

//Class ends
}
