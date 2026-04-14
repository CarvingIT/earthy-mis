<?php

namespace App\Http\Controllers;

use App\Models\Society;
use Illuminate\Http\Request;

class SocietyController extends Controller
{
    public function index()
    {
        $societies = Society::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('societies.index', compact('societies'));
    }

    public function create()
    {
        return view('societies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        Society::create($data + ['user_id' => auth()->id()]);

        return redirect()->route('societies.index')->with('success', 'Society created successfully.');
    }

    public function show(Society $society)
    {
        $this->authorizeOwnership($society);

        return redirect()->route('societies.edit', $society);
    }

    public function edit(Society $society)
    {
        $this->authorizeOwnership($society);

        return view('societies.edit', compact('society'));
    }

    public function update(Request $request, Society $society)
    {
        $this->authorizeOwnership($society);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $society->update($data);

        return redirect()->route('societies.index')->with('success', 'Society updated successfully.');
    }

    public function destroy(Society $society)
    {
        $this->authorizeOwnership($society);

        $society->delete();

        return redirect()->route('societies.index')->with('success', 'Society deleted successfully.');
    }

    private function authorizeOwnership(Society $society): void
    {
        abort_if($society->user_id !== auth()->id(), 403);
    }
}
