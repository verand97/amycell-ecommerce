<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('sort_order')->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100|unique:categories,name',
            'description'=> 'nullable|string|max:500',
            'icon'       => 'nullable|string|max:50',
            'is_active'  => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['slug']      = Str::slug($request->name);
        $validated['is_active'] = $request->boolean('is_active', true);

        Category::create($validated);
        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description'=> 'nullable|string|max:500',
            'icon'       => 'nullable|string|max:50',
            'is_active'  => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['slug']      = Str::slug($request->name);
        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);
        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk.');
        }
        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
