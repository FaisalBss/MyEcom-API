<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShippingRequest;
use App\Http\Requests\SelectAddressRequest;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\PlaceOrderRequest;
use App\Services\CheckoutService;
use App\Services\ShippingService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class CheckoutApiController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService,
        private ShippingService $shippingService,
        private PaymentService $paymentService
    ) {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        [$items, $total, $shipping] = $this->checkoutService->getCartSummary($request->user()->id);

        return response()->json([
            'success' => true,
            'data' => [
                'items'    => $items,
                'total'    => $total,
                'shipping' => $shipping,
            ]
        ]);
    }

    public function shipping(Request $request)
    {
        $addresses = $this->shippingService->getUserAddresses($request->user()->id);

        return response()->json([
            'success' => true,
            'data'    => $addresses,
        ]);
    }

    public function storeShipping(ShippingRequest $request)
    {
        $this->shippingService->storeAddress($request->user()->id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Address saved successfully',
        ], 201);
    }

    public function selectShipping(SelectAddressRequest $request)
    {
        return response()->json([
            'success'             => true,
            'message'             => 'Shipping address selected',
            'shipping_address_id' => $request->shipping_address_id,
        ]);
    }

    public function payment(Request $request)
    {
        $methods = $this->paymentService->getUserMethods($request->user()->id);

        return response()->json([
            'success' => true,
            'data'    => [
                'payment_methods'    => $methods,
                'shipping_address_id'=> $request->get('shipping_address_id'),
            ]
        ]);
    }

    public function savePaymentMethod(PaymentRequest $request)
    {
        $this->paymentService->saveCard($request->user()->id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Card saved successfully',
        ], 201);
    }

    public function placeOrder(PlaceOrderRequest $request)
    {
        try {
            $this->checkoutService->placeOrder($request->user(), $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
            ], 201);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'error'   => 'Order failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
