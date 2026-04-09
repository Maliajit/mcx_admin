<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductRow;
use Illuminate\Http\Request;

class ProductRowController extends Controller
{
    public function index()
    {
        $products = ProductRow::all();
        return view('admin.products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:gold,silver',
            'margin' => 'required|numeric',
            'adjustment' => 'required|numeric',
        ]);
        $data['is_active'] = $request->has('is_active');

        ProductRow::create($data);
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function update(Request $request, ProductRow $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:gold,silver',
            'margin' => 'required|numeric',
            'adjustment' => 'required|numeric',
        ]);
        $data['is_active'] = $request->has('is_active');

        $product->update($data);
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(ProductRow $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
