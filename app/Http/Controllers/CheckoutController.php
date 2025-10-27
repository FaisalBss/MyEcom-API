<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShippingRequest;
use App\Http\Requests\SelectAddressRequest;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\PlaceOrderRequest;
use App\Services\CheckoutService;
use App\Services\ShippingService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
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
        return view('cart.index', compact('items', 'total', 'shipping'));
    }

    public function shipping(Request $request)
    {
        $addresses = $this->shippingService->getUserAddresses($request->user()->id);
        return view('cart.checkout.shipping', compact('addresses'));
    }

    public function storeShipping(ShippingRequest $request)
    {
        $this->shippingService->storeAddress($request->user()->id, $request->validated());
        return back()->with('status', 'Address saved successfully.');
    }

    public function selectShipping(SelectAddressRequest $request)
    {
        return redirect()->route('checkout.payment', [
            'shipping_address_id' => $request->shipping_address_id
        ]);
    }

    public function payment(Request $request)
    {
        $methods = $this->paymentService->getUserMethods($request->user()->id);
        $shippingAddressId = $request->get('shipping_address_id');

        return view('cart.checkout.payment', compact('methods', 'shippingAddressId'));
    }


    public function savePaymentMethod(PaymentRequest $request)
    {
        $this->paymentService->saveCard($request->user()->id, $request->validated());
        return back()->with('status', 'Card saved successfully.');
    }

    public function placeOrder(PlaceOrderRequest $request)
    {
        try {
            $this->checkoutService->placeOrder($request->user(), $request->validated());
            return redirect()->route('user.Orders')->with('status', 'Order placed successfully.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Order failed: ' . $e->getMessage());
        }
    }
}
