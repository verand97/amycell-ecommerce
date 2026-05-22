<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::active()->featured()
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->withCount(['activeProducts'])
            ->get();

        $latestProducts = Product::active()
            ->with('category')
            ->latest()
            ->take(12)
            ->get();

        return view('customer.home', compact('featuredProducts', 'categories', 'latestProducts'));
    }

    public function catalog()
    {
        $query = Product::active()->with('category');

        if (request('category')) {
            $category = Category::where('slug', request('category'))->firstOrFail();
            $query->where('category_id', $category->id);
        }

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (request('type')) {
            $query->where('type', request('type'));
        }

        $sort = request('sort', 'newest');
        match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'popular'    => $query->orderBy('sold_count', 'desc'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('customer.catalog.index', compact('products', 'categories'));
    }

    public function showProduct(Product $product)
    {
        abort_if(!$product->is_active, 404);
        $product->load('category');

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('customer.catalog.show', compact('product', 'related'));
    }
}
