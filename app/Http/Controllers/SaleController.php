<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Stock;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = Sale::latest()->get();
        return view('sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        $customers = Customer::all();
        return view('sales.create', compact('products','customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'Date' => ['nullable', 'date'],
            'product_id' => ['nullable','integer'],
            'quantity' => ['nullable', 'integer'],
            'customer_id' => ['nullable','integer'],
            'rate' => ['nullable', 'numeric'],
            'amount' => ['nullable', 'numeric'],
        ]);

        Sale::create($data);
        $stock = Stock::where('product_id', $request->product_id)->first();
        if(!empty($stock->id)){
        $quantity = $stock->quantity;
        $new_quantity = $quantity - $request->quantity;
        $stock->quantity = $new_quantity;
        $stock->save();
        }

        return redirect()->route('sale.index')->with('success', 'Sale created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        return redirect()->route('sales.edit', $sale);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        $products = Product::all();
        $customers = Customer::all();
        return view('sales.edit', compact('sale','products','customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $old_quantity = $sale->quantity;
        $data = $request->validate([
            'Date' => ['nullable', 'date'],
            'product_id' => ['nullable','integer'],
            'quantity' => ['nullable', 'integer'],
            'customer_id' => ['nullable','integer'],
            'rate' => ['nullable', 'numeric'],
            'amount' => ['nullable', 'numeric'],
        ]);

        $sale->update($data);
        $stock = Stock::where('product_id', $request->product_id)->first();
        if(!empty($stock->id) && $old_quantity != $request->quantity){
        $quantity = $stock->quantity;
        $new_quantity = $quantity - $request->quantity;
        $stock->quantity = $new_quantity;
        $stock->save();
        }

        return redirect()->route('sale.index')->with('success', 'Sale updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();
        return redirect()->route('sale.index')->with('success', 'Sale deleted successfully.');
    }
}
