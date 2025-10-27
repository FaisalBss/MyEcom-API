<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\UpdateCartRequest;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cartService)
    {
        $this->middleware('auth:api');
    }

    public function view()
    {
        [$items, $total] = $this->cartService->getUserCart(auth()->id());

        return view('cart.index', compact('items', 'total'));
    }

    public function add(Product $product, Request $request)
    {
        $message = $this->cartService->addToCart(auth()->id(), $product, $request);
        return back()->with('status', $message);
    }

    public function update(UpdateCartRequest $request, Product $product)
    {
        $message = $this->cartService->updateCart(auth()->id(), $product, $request->quantity);
        return back()->with('status', $message);
    }

    public function remove(Product $product)
    {
        $message = $this->cartService->removeFromCart(auth()->id(), $product);
        return back()->with('status', $message);
    }

    public function clear()
    {
        $this->cartService->clearCart(auth()->id());
        return back()->with('status', 'Cart cleared.');
    }
}
