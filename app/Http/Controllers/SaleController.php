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
        $products = Product::with('salesUnit')->get();
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
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'numeric', 'min:0'], // This is in SALES UNIT
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'rate' => ['required', 'numeric', 'min:0'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        // Get product with unit information
        $product = Product::with(['baseUnit', 'salesUnit'])->findOrFail($data['product_id']);
        
        // Convert sales quantity (in sales unit) to base units for stock tracking
        $quantityInBaseUnits = $product->convertSalesToBase($data['quantity']);
        
        // Check if sufficient stock is available (stock is tracked in base units)
        $currentStock = $product->getCurrentStock();
        if ($currentStock < $quantityInBaseUnits) {
            $availableInSalesUnit = $product->getCurrentStockInSalesUnit();
            $salesUnitName = $product->salesUnit->name ?? 'units';
            $baseUnitName = $product->baseUnit->name ?? 'units';
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['quantity' => "Insufficient stock! Available: {$availableInSalesUnit} {$salesUnitName} ({$currentStock} {$baseUnitName})"]); 
        }

        // Create the sale record (quantity stored in sales unit)
        $sale = Sale::create($data);

        // Create stock transaction (quantity stored in base units)
        $stock = new Stock();
        $stock->product_id = $data['product_id'];
        $stock->Date = $data['Date'] ?? now()->toDateString();
        $stock->quantity = -$quantityInBaseUnits; // Negative because it's a sale (in base units)
        $stock->new_adjustment_in_stock = $quantityInBaseUnits;
        $stock->action = 'sold';
        $stock->transaction_type = 'sale';
        $stock->reference_id = $sale->id;
        
        // Create descriptive notes showing both units
        $salesUnitName = $product->salesUnit->name ?? 'units';
        $baseUnitName = $product->baseUnit->name ?? 'units';
        $stock->notes = "Sale #{$sale->id} - {$data['quantity']} {$salesUnitName} ({$quantityInBaseUnits} {$baseUnitName})";
        $stock->save();

        $salesUnitName = $product->salesUnit->name ?? 'units';
        $baseUnitName = $product->baseUnit->name ?? 'units';
        
        return redirect()->route('sale.index')->with('success', 
            "Sale created successfully. Stock reduced by {$quantityInBaseUnits} {$baseUnitName} ({$data['quantity']} {$salesUnitName})");
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
        $products = Product::with('salesUnit')->get();
        $customers = Customer::all();
        return view('sales.edit', compact('sale','products','customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $oldQuantity = $sale->quantity;
        $oldProductId = $sale->product_id;
        
        $data = $request->validate([
            'Date' => ['nullable', 'date'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'rate' => ['required', 'numeric', 'min:0'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        // Get product with unit information
        $product = Product::with(['baseUnit', 'salesUnit'])->findOrFail($data['product_id']);
        
        // Convert quantities to base units
        $oldQuantityInBase = $sale->product->convertSalesToBase($oldQuantity);
        $newQuantityInBase = $product->convertSalesToBase($data['quantity']);
        
        // Calculate the difference
        $quantityDifference = $newQuantityInBase - $oldQuantityInBase;
        
        // If quantity changed, update stock
        if ($quantityDifference != 0) {
            // Check if sufficient stock is available (only if increasing sale quantity)
            if ($quantityDifference > 0) {
                $currentStock = $product->getCurrentStock();
                // Add back old stock first since we're calculating from current state
                $availableStock = $currentStock + $oldQuantityInBase;
                
                if ($availableStock < $newQuantityInBase) {
                    $availableBags = $product->getCurrentStockInSalesUnit();
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['quantity' => "Insufficient stock! Available: {$availableBags} {$product->salesUnit->name}"]); 
                }
            }
            
            // Find and delete the old stock transaction for this sale
            $oldStockTransaction = Stock::where('reference_id', $sale->id)
                ->where('transaction_type', 'sale')
                ->first();
            
            if ($oldStockTransaction) {
                $oldStockTransaction->delete();
            }
            
            // Create new stock transaction with updated quantity
            $stock = new Stock();
            $stock->product_id = $data['product_id'];
            $stock->Date = $data['Date'] ?? now()->toDateString();
            $stock->quantity = -$newQuantityInBase; // Negative because it's a sale
            $stock->new_adjustment_in_stock = $newQuantityInBase;
            $stock->action = 'sold';
            $stock->transaction_type = 'sale';
            $stock->reference_id = $sale->id;
            $stock->notes = "Sale #{$sale->id} (Updated) - {$data['quantity']} {$product->salesUnit->name} ({$newQuantityInBase} {$product->baseUnit->name})";
            $stock->save();
        }

        $sale->update($data);

        return redirect()->route('sale.index')->with('success', 
            "Sale updated successfully. Stock adjusted by " . abs($quantityDifference) . " {$product->baseUnit->name}");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        // Find and delete the associated stock transaction
        $stockTransaction = Stock::where('reference_id', $sale->id)
            ->where('transaction_type', 'sale')
            ->first();
        
        if ($stockTransaction) {
            $stockTransaction->delete();
        }
        
        $sale->delete();
        return redirect()->route('sale.index')->with('success', 'Sale deleted successfully. Stock has been restored.');
    }
}
