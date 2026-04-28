<?php

namespace App\Http\Controllers;

use App\Models\Consumable;
use Illuminate\Http\Request;

class ConsumableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $consumables = Consumable::latest()->get();
        return view('consumable.index', compact('consumables'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('consumable.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'item' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        Consumable::create($data);

        return redirect()->route('consumables.index')->with('success', 'Consumable created successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Consumable $consumable)
    {
         return redirect()->route('consumables.edit', $consumable);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Consumable $consumable)
    {
        return view('consumable.edit', compact('consumable'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Consumable $consumable)
    {
        $data = $request->validate([
            'item' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $consumable->update($data);

        return redirect()->route('consumables.index')->with('success', 'Consumable created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Consumable $consumable)
    {
        $consumable->delete();
        return redirect()->route('consumables.index')->with('success', 'Consumable deleted successfully.');
    }
}
