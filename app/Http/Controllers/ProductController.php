<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $query = Product::with('materials', 'orders');

        // Search functionality
        if ($search = request('search')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $products = $query->get();
        $search = request('search');
        
        return view('inventory.products.index', compact('products', 'search'));
    }

    public function create()
    {
        return view('inventory.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:products',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product berhasil ditambahkan');
    }

    public function edit(Product $product)
    {
        return view('inventory.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:products,name,' . $product->id,
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product berhasil diperbarui');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product berhasil dihapus');
    }
}
