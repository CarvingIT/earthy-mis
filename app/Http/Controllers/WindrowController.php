<?php

namespace App\Http\Controllers;

use App\Models\Windrow;
use Illuminate\Http\Request;

class WindrowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $windrow = Windrow::latest()->get();
        return view('windrow.index', compact('windrow'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('windrow.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'windrow_number' => ['nullable','integer'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'weight_in' => ['nullable', 'string', 'max:255'],
            'out_date' => ['nullable', 'date'],
            'screening_date' => ['nullable', 'date'],
        ]);

        Windrow::create($data);

        return redirect()->route('windrow.index')->with('success', 'Windrow created successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(windrow $windrow)
    {
         return redirect()->route('windrow.edit', $windrow);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(windrow $windrow)
    {
        return view('windrow.edit', compact('windrow'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, windrow $windrow)
    {
        $data = $request->validate([
            'windrow_number' => ['nullable','integer'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'weight_in' => ['nullable', 'string', 'max:255'],
            'out_date' => ['nullable', 'date'],
            'screening_date' => ['nullable', 'date'],
        ]);

        $windrow->update($data);
        return redirect()->route('windrow.index')->with('success', 'Windrow updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(windrow $windrow)
    {
        $windrow->delete();
        return redirect()->route('windrow.index')->with('success', 'Windrow deleted successfully.');

    }
}
