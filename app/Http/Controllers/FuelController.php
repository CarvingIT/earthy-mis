<?php

namespace App\Http\Controllers;

use App\Models\Fuel;
use Illuminate\Http\Request;

class FuelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fuels = Fuel::latest()->get();
        return view('fuel.index', compact('fuels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('fuel.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'Date' => ['nullable', 'date'],
            'quantity' => ['nullable', 'integer'],
            'type' => ['nullable', 'string', 'max:255'],
        ]);

        Fuel::create($data);

        return redirect()->route('fuel.index')->with('success', 'Fuel created successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Fuel $fuel)
    {
         return redirect()->route('fuel.edit', $fuel);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fuel $fuel)
    {
        return view('fuel.edit', compact('fuel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fuel $fuel)
    {
        $data = $request->validate([
            'Date' => ['nullable', 'date'],
            'quantity' => ['nullable', 'integer'],
            'type' => ['nullable', 'string', 'max:255'],
        ]);

        $fuel->update($data);

        return redirect()->route('fuel.index')->with('success', 'Fuel created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fuel $fuel)
    {
        $fuel->delete();
        return redirect()->route('fuel.index')->with('success', 'Fuel deleted successfully.');
    }
}
