<?php

namespace App\Http\Controllers;

use App\Models\Consumable;
use App\Models\SupplyItem;
use Illuminate\Http\Request;

class SupplyItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $supplyitems = SupplyItem::latest()->get();
        return view('supplyitem.index', compact('supplyitems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $consumables = Consumable::all();
        return view('supplyitem.create', compact('consumables'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'Date' => ['nullable', 'date'],
            'quantity' => ['nullable', 'integer'],
            'consumable_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string'],
        ]);

        SupplyItem::create($data);

        return redirect()->route('supplyitems.index')->with('success', 'SupplyItem created successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(SupplyItem $supplyitem)
    {
         return redirect()->route('supplyitems.edit', $supplyitem);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplyItem $supplyitem)
    {
        $consumables = Consumable::all();
        return view('supplyitem.edit', compact('supplyitem','consumables'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SupplyItem $supplyitem)
    {
        $data = $request->validate([
            'Date' => ['nullable', 'date'],
            'quantity' => ['nullable', 'integer'],
            'consumable_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string'],
        ]);

        $supplyitem->update($data);

        return redirect()->route('supplyitems.index')->with('success', 'Supply Item created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplyItem $supplyitem)
    {
        $supplyitem->delete();
        return redirect()->route('supplyitems.index')->with('success', 'Supply Item deleted successfully.');
    }
}
