<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Create/Checkout new order.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createOrder($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'data' => new OrderResource($order),
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage(),
            ], 422);
        }
    }
    /**
     * Fetch order details by order number for tracking and receipt viewing.
     */
    public function show(string $orderNumber): JsonResponse
    {
        $order = \App\Models\Order::with('items')->where('order_number', $orderNumber)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order),
        ]);
    }

    /**
     * Simulate a fake/demo payment confirmation on a placed order.
     */
    public function fakePay(string $orderNumber): JsonResponse
    {
        $order = \App\Models\Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
            'notes' => 'Demo Payment Verified · TXN-DEMO-' . strtoupper(\Illuminate\Support\Str::random(6)),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Demo payment authorized and confirmed successfully!',
            'data' => new OrderResource($order->fresh('items')),
        ]);
    }
}
