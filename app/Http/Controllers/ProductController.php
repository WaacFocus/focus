<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%$s%")
                ->orWhere('sku', 'like', "%$s%")
                ->orWhere('category', 'like', "%$s%"));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $products   = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = Product::whereNotNull('category')->distinct()->pluck('category')->sort();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku'         => 'nullable|string|max:100',
            'unit_price'  => 'required|numeric|min:0',
            'unit'        => 'required|string|max:50',
            'category'    => 'nullable|string|max:100',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product created.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku'         => 'nullable|string|max:100',
            'unit_price'  => 'required|numeric|min:0',
            'unit'        => 'required|string|max:50',
            'category'    => 'nullable|string|max:100',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        abort_unless(auth()->user()->isManager(), 403);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }
}
