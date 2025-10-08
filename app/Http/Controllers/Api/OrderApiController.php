<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function userOrders(Request $request)
    {
        $orders = Order::with(['items.product'])
            ->where('user_id', $request->user()->id)
            ->paginate(10);

        return response()->json([
            'success' => true,
            'orders'  => $orders,
        ]);
    }

    public function show($id, Request $request)
    {
        $order = Order::with(['items.product'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'order'   => $order,
        ]);
    }
}
