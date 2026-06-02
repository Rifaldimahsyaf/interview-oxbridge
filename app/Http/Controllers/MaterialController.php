<?php

namespace App\Http\Controllers;

use App\Models\ListMaterial;
use App\Models\Product;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $query = ListMaterial::with('product');

        // Search functionality
        if ($search = request('search')) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhereHas('product', function ($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
        }

        $materials = $query->get();
        $search = request('search');
        
        return view('inventory.materials.index', compact('materials', 'search'));
    }

    public function create()
    {
        $products = Product::all();
        return view('inventory.materials.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'quantity_needed' => 'required|integer|min:1',
        ]);

        $material = ListMaterial::create($validated);

        return redirect()->route('materials.index')->with('success', 'Material berhasil ditambahkan');
    }

    public function edit(ListMaterial $material)
    {
        $products = Product::all();
        return view('inventory.materials.edit', compact('material', 'products'));
    }

    public function update(Request $request, ListMaterial $material)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'quantity_needed' => 'required|integer|min:1',
        ]);

        $oldProductId = $material->product_id;
        $material->update($validated);

        // Update quantity untuk kedua product jika product_id berubah
        if ($oldProductId !== $validated['product_id']) {
            Product::find($oldProductId)->updateQuantity();
        }

        return redirect()->route('materials.index')->with('success', 'Material berhasil diperbarui');
    }

    public function destroy(ListMaterial $material)
    {
        $productId = $material->product_id;
        $material->delete();
        
        // Trigger updateQuantity untuk product
        Product::find($productId)->updateQuantity();
        
        return redirect()->route('materials.index')->with('success', 'Material berhasil dihapus');
    }
}
