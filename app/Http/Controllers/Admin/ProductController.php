<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        $products   = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'category_id'      => 'required|exists:categories,id',
            'description'      => 'nullable|string',
            'full_description' => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'sale_price'       => 'nullable|numeric|min:0',
            'stock'            => 'required|integer|min:0',
            'type'             => 'required|in:physical,digital',
            'sku'              => 'nullable|string|unique:products,sku',
            'weight'           => 'nullable|numeric|min:0',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'digital_file'     => 'nullable|file|max:51200',
            'is_active'        => 'boolean',
            'is_featured'      => 'boolean',
        ]);

        $validated['slug'] = Str::slug($request->name) . '-' . Str::random(5);

        // Ensure sale_price is less than price if provided
        if (!empty($validated['sale_price']) && $validated['sale_price'] >= $validated['price']) {
            return back()->withInput()->withErrors(['sale_price' => 'Harga promo harus lebih kecil dari harga normal.']);
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        if ($request->hasFile('digital_file')) {
            $validated['digital_file'] = $request->file('digital_file')->store('digital-products');
        }

        $validated['is_active']   = $request->boolean('is_active', true);
        $validated['is_featured']  = $request->boolean('is_featured');

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'category_id'      => 'required|exists:categories,id',
            'description'      => 'nullable|string',
            'full_description' => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'sale_price'       => 'nullable|numeric|min:0',
            'stock'            => 'required|integer|min:0',
            'type'             => 'required|in:physical,digital',
            'sku'              => 'nullable|string|unique:products,sku,' . $product->id,
            'weight'           => 'nullable|numeric|min:0',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'digital_file'     => 'nullable|file|max:51200',
            'is_active'        => 'boolean',
            'is_featured'      => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        if ($request->hasFile('digital_file')) {
            $validated['digital_file'] = $request->file('digital_file')->store('digital-products');
        }

        $validated['is_active']  = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');

        // Ensure sale_price is less than price if provided
        if (!empty($validated['sale_price']) && $validated['sale_price'] >= $validated['price']) {
            return back()->withInput()->withErrors(['sale_price' => 'Harga promo harus lebih kecil dari harga normal.']);
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->update(['is_active' => false]);
        return redirect()->route('admin.products.index')
            ->with('success', 'Produk dinonaktifkan.');
    }

    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Produk berhasil {$status}.");
    }
}
