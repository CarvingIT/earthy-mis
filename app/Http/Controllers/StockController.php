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
        $stocks = Stock::with(['product.baseUnit', 'product.salesUnit'])
            ->orderBy('Date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        
        // Calculate current stock for each product
        $currentStocks = Stock::selectRaw('product_id, SUM(quantity) as total_quantity')
            ->groupBy('product_id')
            ->pluck('total_quantity', 'product_id');
        
        // Pre-calculate cumulative totals for each stock transaction
        $cumulativeTotals = [];
        foreach ($stocks as $stock) {
            $key = "{$stock->product_id}_{$stock->id}";
            $cumulativeTotals[$key] = Stock::where('product_id', $stock->product_id)
                ->where('id', '<=', $stock->id)
                ->sum('quantity');
        }
        
        return view('stock.index', compact('stocks', 'currentStocks', 'cumulativeTotals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::with(['baseUnit', 'salesUnit'])->get();
        return view('stock.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'Date' => ['nullable', 'date'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_type' => ['required', 'in:base,sales'], // User chooses which unit they're using
            'transaction_type' => ['required', 'in:purchase,adjustment,return,opening_stock'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Get product with unit information
        $product = Product::with(['baseUnit', 'salesUnit'])->findOrFail($data['product_id']);
        
        // Convert quantity to base units based on user's selection
        $quantityInBaseUnits = 0;
        if ($data['unit_type'] === 'sales') {
            // User entered quantity in sales unit, convert to base
            $quantityInBaseUnits = $product->convertSalesToBase($data['quantity']);
        } else {
            // User entered quantity in base unit, use as-is
            $quantityInBaseUnits = $data['quantity'];
        }
        
        // Get current stock for this product (already in base units)
        $currentStock = Stock::where('product_id', $data['product_id'])
            ->sum('quantity');
        
        // Calculate new cumulative total
        $newCumulativeStock = $currentStock + $quantityInBaseUnits;

        $stock = new Stock();
        $stock->product_id = $data['product_id'];
        $stock->Date = $data['Date'] ?? now()->toDateString();
        $stock->quantity = $quantityInBaseUnits; // Always stored in base units
        $stock->new_adjustment_in_stock = $quantityInBaseUnits;
        $stock->action = 'added';
        $stock->transaction_type = $data['transaction_type'];
        $stock->notes = $data['notes'] ?? null;
        
        $stock->save();

        return redirect()->route('stock.index')->with('success', 'Stock added successfully.');
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
        $products = Product::with(['baseUnit', 'salesUnit'])->get();
        return view('stock.edit', compact('stock','products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stock $stock)
    {
        $data = $request->validate([
            'Date' => ['nullable', 'date'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_type' => ['required', 'in:base,sales'],
            'transaction_type' => ['required', 'in:purchase,adjustment,return,opening_stock'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Get product with unit information
        $product = Product::with(['baseUnit', 'salesUnit'])->findOrFail($data['product_id']);
        
        // Convert quantity to base units based on user's selection
        $quantityInBaseUnits = 0;
        if ($data['unit_type'] === 'sales') {
            // User entered quantity in sales unit, convert to base
            $quantityInBaseUnits = $product->convertSalesToBase($data['quantity']);
        } else {
            // User entered quantity in base unit, use as-is
            $quantityInBaseUnits = $data['quantity'];
        }

        $stock->Date = $data['Date'] ?? now()->toDateString();
        $stock->product_id = $data['product_id'];
        $stock->quantity = $quantityInBaseUnits; // Always stored in base units
        $stock->new_adjustment_in_stock = $quantityInBaseUnits;
        $stock->transaction_type = $data['transaction_type'];
        $stock->notes = $data['notes'] ?? null;
        
        $stock->save();

        return redirect()->route('stock.index')->with('success', 'Stock transaction updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock)
    {
        $stock->delete();
        return redirect()->route('stock.index')->with('success', 'Stock deleted successfully.');
    }

    /**
     * Sync Sales with stock transactions.
     */
    public function syncSales()
    {
        $sales = \App\Models\Sale::all();
        $syncedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        foreach ($sales as $sale) {
            try {
                // Check if this sale already has a stock transaction
                $existingStock = \App\Models\Stock::where('reference_id', $sale->id)
                    ->where('transaction_type', 'sale')
                    ->first();

                if ($existingStock) {
                    $skippedCount++;
                    continue;
                }

                // Get product with unit information
                $product = \App\Models\Product::with(['baseUnit', 'salesUnit'])->find($sale->product_id);
                
                if (!$product) {
                    $errorCount++;
                    continue;
                }

                // Convert sales quantity to base units
                $quantityInBaseUnits = $product->convertSalesToBase($sale->quantity);

                // Create stock transaction
                $stock = new \App\Models\Stock();
                $stock->product_id = $sale->product_id;
                $stock->Date = $sale->Date ?? now()->toDateString();
                $stock->quantity = -$quantityInBaseUnits; // Negative because it's a sale
                $stock->new_adjustment_in_stock = $quantityInBaseUnits;
                $stock->action = 'sold';
                $stock->transaction_type = 'sale';
                $stock->reference_id = $sale->id;
                
                // Create descriptive notes showing both units
                $salesUnitName = $product->salesUnit->name ?? 'units';
                $baseUnitName = $product->baseUnit->name ?? 'units';
                $stock->notes = "Sale #{$sale->id} - {$sale->quantity} {$salesUnitName} ({$quantityInBaseUnits} {$baseUnitName}) [Historical Sync]";
                $stock->save();

                $syncedCount++;

            } catch (\Exception $e) {
                $errorCount++;
            }
        }

        $message = "Sync complete! Synced: {$syncedCount}, Already synced: {$skippedCount}";
        if ($errorCount > 0) {
            $message .= ", Errors: {$errorCount}";
        }

        return redirect()->route('stock.index')->with('success', $message);
    }
}
