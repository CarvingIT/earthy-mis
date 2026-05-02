<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $units = Unit::all();
        return view('products.create', compact('units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'base_unit_id' => ['nullable', 'integer', 'exists:units,id'],
        ]);

        Product::create($data + ['user_id' => auth()->id()]);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $this->authorizeOwnership($product);

        return redirect()->route('products.edit', $product);
    }

    public function edit(Product $product)
    {
        $this->authorizeOwnership($product);
        $units = Unit::all();

        return view('products.edit', compact('product', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeOwnership($product);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'base_unit_id' => ['nullable', 'integer', 'exists:units,id'],
        ]);

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->authorizeOwnership($product);

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    private function authorizeOwnership(Product $product): void
    {
        abort_if($product->user_id !== auth()->id(), 403);
    }

    public function getProductRate($product_id){
        $product_details = Product::find($product_id);
        return json_encode(['rate'=>$product_details->price]);
    }
}
