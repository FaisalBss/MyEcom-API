<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\UpdateCartRequest;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartApiController extends Controller
{
    public function __construct(private CartService $cartService)
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        [$items, $total] = $this->cartService->getUserCart(auth()->id());

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
                'total' => $total,
            ]
        ]);
    }

    public function store(Product $product, Request $request)
    {
        $message = $this->cartService->addToCart(auth()->id(), $product, $request);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function update(UpdateCartRequest $request, Product $product)
    {
        $message = $this->cartService->updateCart(auth()->id(), $product, $request->quantity);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function destroy(Product $product)
    {
        $message = $this->cartService->removeFromCart(auth()->id(), $product);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function clear()
    {
        $this->cartService->clearCart(auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared.',
        ]);
    }
}
