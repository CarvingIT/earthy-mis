<?php

namespace App\Http\Controllers;

use App\Models\Turning;
use App\Models\Windrow;
use Illuminate\Http\Request;

class TurningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $turnings = Turning::latest()->get();
        return view('turning.index', compact('turnings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $windrow = Windrow::all();
        return view('turning.create', compact('windrow'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'windrow_id' => ['nullable','max:255'],
            'Date' => ['nullable', 'date'],
            'duration' => ['nullable', 'string', 'max:255'],
        ]);

        Turning::create($data);

        return redirect()->route('turning.index')->with('success', 'Turning created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Turning $turning)
    {
        return redirect()->route('turning.edit', $turning);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Turning $turning)
    {
        $windrow = Windrow::all();
        return view('turning.edit', compact('turning','windrow'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Turning $turning)
    {
        $data = $request->validate([
            'windrow_id' => ['nullable','max:255'],
            'Date' => ['nullable', 'date'],
            'duration' => ['nullable', 'string', 'max:255'],
        ]);

        $turning->update($data);

        return redirect()->route('turning.index')->with('success', 'Turning updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Turning $turning)
    {
        $turning->delete();
        return redirect()->route('turning.index')->with('success', 'Turning deleted successfully.');
    }
}
