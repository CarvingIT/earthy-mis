<?php

namespace App\Http\Controllers;

use App\Models\Jcb;
use Illuminate\Http\Request;

class JcbController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jcbs = Jcb::latest()->get();
        return view('jcb.index', compact('jcbs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('jcb.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'Date' => ['nullable', 'date'],
            'duration' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Jcb::create($data);

        return redirect()->route('jcb.index')->with('success', 'JCB entry created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Jcb $jcb)
    {
        return redirect()->route('jcb.edit', $jcb);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jcb $jcb)
    {
        return view('jcb.edit', compact('jcb'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jcb $jcb)
    {
        $data = $request->validate([
            'Date' => ['nullable', 'date'],
            'duration' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $jcb->update($data);

        return redirect()->route('jcb.index')->with('success', 'JCB entry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jcb $jcb)
    {
        $jcb->delete();
        return redirect()->route('jcb.index')->with('success', 'JCB entry deleted successfully.');
    }
}
