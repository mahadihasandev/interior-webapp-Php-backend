<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Create an order with items in a transaction.
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $itemsData = $data['items'];
            $subtotal = 0;
            $preparedItems = [];

            foreach ($itemsData as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                $itemTotal = (float) $product->price * $quantity;
                $subtotal += $itemTotal;

                $preparedItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'subtotal' => $itemTotal,
                ];

                // Decrement stock if available
                if ($product->stock >= $quantity) {
                    $product->decrement('stock', $quantity);
                }
            }

            $shippingFee = $subtotal > 500 ? 0.00 : 25.00;
            $totalAmount = $subtotal + $shippingFee;

            $paymentMethod = $data['payment_method'] ?? 'demo_card';
            $isDemoOrPaid = in_array($paymentMethod, ['demo_card', 'fake_payment', 'mock_card']);

            $order = Order::create([
                'order_number' => 'INT-' . strtoupper(Str::random(8)),
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'shipping_address' => $data['shipping_address'],
                'city' => $data['city'],
                'postal_code' => $data['postal_code'],
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total_amount' => $totalAmount,
                'status' => $isDemoOrPaid ? 'processing' : 'pending',
                'payment_method' => $paymentMethod,
                'payment_status' => $isDemoOrPaid ? 'paid' : 'pending',
                'notes' => $data['notes'] ?? ($isDemoOrPaid ? 'Demo Payment Simulated · TXN-DEMO-' . strtoupper(Str::random(6)) : null),
            ]);

            foreach ($preparedItems as $item) {
                $order->items()->create($item);
            }

            return $order->load('items');
        });
    }
}
