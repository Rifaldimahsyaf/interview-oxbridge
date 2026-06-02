<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $query = Order::with('product');

        // Search functionality
        if ($search = request('search')) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })->orWhere('status', 'like', '%' . $search . '%');
        }

        $orders = $query->get();
        $search = request('search');
        
        return view('inventory.orders.index', compact('orders', 'search'));
    }

    public function create()
    {
        $products = Product::where('quantity', '>', 0)->get();
        return view('inventory.orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::find($validated['product_id']);

        if ($validated['quantity'] > $product->quantity) {
            return redirect()->back()->withErrors(['quantity' => "Quantity tidak cukup. Tersedia: {$product->quantity}"]);
        }

        // Kurangi quantity material berdasarkan order
        $materials = $product->materials;
        foreach ($materials as $material) {
            $quantityToDecrease = $material->quantity_needed * $validated['quantity'];
            $material->update([
                'quantity' => $material->quantity - $quantityToDecrease
            ]);
        }

        // Update product quantity
        $product->updateQuantity();

        // Buat order
        Order::create($validated);

        return redirect()->route('orders.index')->with('success', 'Order berhasil dibuat dan quantity material berkurang');
    }

    public function destroy(Order $order)
    {
        $product = $order->product;
        
        // Restore quantity material ketika order dihapus
        $materials = $product->materials;
        foreach ($materials as $material) {
            $quantityToRestore = $material->quantity_needed * $order->quantity;
            $material->update([
                'quantity' => $material->quantity + $quantityToRestore
            ]);
        }

        // Update product quantity
        $product->updateQuantity();

        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order berhasil dihapus dan quantity material dikembalikan');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $order->update($validated);

        return redirect()->route('orders.index')->with('success', 'Status order berhasil diperbarui');
    }
}
