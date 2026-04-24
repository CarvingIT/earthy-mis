<?php

namespace App\Http\Controllers;

use App\Models\Weight;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class WeightController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $weights = Weight::latest()->get();
        return view('weights.index', compact('weights'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicles = Vehicle::all();
        return view('weights.create', compact('vehicles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'Date' => ['nullable', 'date'],
            'vehicle_id' => ['nullable', 'integer'],
            'gross_weight' => ['nullable', 'integer'],
            'tare_weight' => ['nullable', 'integer'],
            'net_weight' => ['nullable', 'integer'],
            'number_of_buckets' =>['nullable','integer']
        ]);

        Weight::create($data);

        return redirect()->route('weights.index')->with('success', 'Weights created successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Weight $weight)
    {
        return redirect()->route('weights.edit', $weight);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Weight $weight)
    {
        $vehicles = Vehicle::all();
        return view('weights.edit', compact('weight','vehicles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Weight $weight)
    {
        $data = $request->validate([
            'Date' => ['nullable', 'date'],
            'vehicle_id' => ['nullable', 'integer'],
            'gross_weight' => ['nullable', 'integer'],
            'tare_weight' => ['nullable', 'integer'],
            'net_weight' => ['nullable', 'integer'],
            'number_of_buckets' =>['nullable','integer']
        ]);

        $weight->update($data);

        return redirect()->route('weights.index')->with('success', 'Weight updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Weight $weight)
    {
        $weight->delete();
        return redirect()->route('weights.index')->with('success', 'Weight deleted successfully.');
    }
}
