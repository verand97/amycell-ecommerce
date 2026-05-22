<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return view('customer.cart.index', compact('cart', 'total'));
    }

    public function add(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        if ($product->isPhysical() && $product->stock < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $cart = Session::get('cart', []);
        $key  = $product->id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $request->quantity;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'price'      => (float) ($product->sale_price ?? $product->price),
                'image'      => $product->image,
                'type'       => $product->type,
                'quantity'   => $request->quantity,
                'slug'       => $product->slug,
            ];
        }

        Session::put('cart', $cart);

        return back()->with('success', "«{$product->name}» berhasil ditambahkan ke keranjang.");
    }

    public function update(Request $request, int $productId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $cart = Session::get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $request->quantity;
            Session::put('cart', $cart);
        }

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function remove(int $productId)
    {
        $cart = Session::get('cart', []);
        unset($cart[$productId]);
        Session::put('cart', $cart);

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function clear()
    {
        Session::forget('cart');
        return back()->with('success', 'Keranjang dikosongkan.');
    }

    public function count()
    {
        $cart = Session::get('cart', []);
        return response()->json(['count' => count($cart)]);
    }
}
