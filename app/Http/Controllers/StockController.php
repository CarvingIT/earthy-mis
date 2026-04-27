<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stocks = Stock::latest()->get();
        $sum = [];
        foreach($stocks as $stock){
            $sum[$stock->product_id] = ($sum[$stock->product_id] ?? 0) + $stock->quantity;
        }
        return view('stock.index', compact('stocks','sum'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        return view('stock.create', compact('products'));
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
        ]);

        $previous_stock = Stock::where('product_id',$request->product_id)->latest()->first();
        $new_stock = $previous_stock->quantity + $request->quantity; 

        $stock = new Stock();
        $stock->product_id = $data['product_id'];
        $stock->Date = $data['Date'];
        $stock->quantity=$new_stock;
        $stock->new_adjustment_in_stock = $request->quantity;
        $stock->action = 'added';
        $stock->save();

        return redirect()->route('stock.index')->with('success', 'Stock created successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Stock $stock)
    {
        return redirect()->route('stock.edit', $stock);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stock $stock)
    {
        $products = Product::all();
        return view('stock.edit', compact('stock','products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stock $stock)
    {
        $data = $request->validate([
            'Date' => ['nullable', 'date'],
            'product_id' => ['nullable','integer'],
            'quantity' => ['nullable', 'integer'],
        ]);

        $stock->update($data);

        return redirect()->route('stock.index')->with('success', 'Stock updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock)
    {
        $stock->delete();
        return redirect()->route('stock.index')->with('success', 'Stock deleted successfully.');
    }
}
