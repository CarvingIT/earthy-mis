<?php

namespace App\Http\Controllers;

use App\Models\Society;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SocietyController extends Controller
{
    public function index()
    {
        $societies = Society::latest()->get();

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
            'joining_month' => ['nullable', 'string', 'max:255'],
            'flats_families' => ['nullable', 'string', 'max:255'],
            'chairman_name' => ['nullable', 'string', 'max:255'],
            'secretary_name' => ['nullable', 'string', 'max:255'],
            'contact_person_email' => ['nullable', 'string', 'lowercase', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'rate_per_flat' => ['nullable', 'numeric', 'min:0'],
            'billing_amount' => ['nullable', 'numeric', 'min:0'],
            'vehicle_number' => ['nullable', 'string', 'max:255'],
            'mou_end_date' => ['nullable', 'date'],
            'mou_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ]);

        if ($request->hasFile('mou_file')) {
            $data['mou_file'] = $request->file('mou_file')->store('society_mous', 'public');
        }

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
            'joining_month' => ['nullable', 'string', 'max:255'],
            'flats_families' => ['nullable', 'string', 'max:255'],
            'chairman_name' => ['nullable', 'string', 'max:255'],
            'secretary_name' => ['nullable', 'string', 'max:255'],
            'contact_person_email' => ['nullable', 'string', 'lowercase', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'rate_per_flat' => ['nullable', 'numeric', 'min:0'],
            'billing_amount' => ['nullable', 'numeric', 'min:0'],
            'vehicle_number' => ['nullable', 'string', 'max:255'],
            'mou_end_date' => ['nullable', 'date'],
            'mou_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ]);

        if ($request->hasFile('mou_file')) {
            if ($society->mou_file && Storage::disk('public')->exists($society->mou_file)) {
                Storage::disk('public')->delete($society->mou_file);
            }
            $data['mou_file'] = $request->file('mou_file')->store('society_mous', 'public');
        }

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
        // Allowed for all users for now
    }
}
