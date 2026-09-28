<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ReadyMadeOrderController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'all');
        $search = $request->query('search', '');

        $query = Order::with(['items.product.category', 'items.product.brand'])->latest();

        if ($statusFilter !== 'all' && !empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        // High-level statistics
        $allOrders = Order::all();
        $totalRevenue = $allOrders->where('status', '!=', 'cancelled')->sum('total_amount');
        $totalUnits = \App\Models\OrderItem::sum('quantity');
        $pendingCount = $allOrders->where('status', 'pending')->count();
        $processingCount = $allOrders->where('status', 'processing')->count();
        $shippedCount = $allOrders->where('status', 'shipped')->count();
        $deliveredCount = $allOrders->where('status', 'delivered')->count();

        return view('seller.orders.ready-made', compact(
            'orders',
            'statusFilter',
            'search',
            'totalRevenue',
            'totalUnits',
            'pendingCount',
            'processingCount',
            'shippedCount',
            'deliveredCount'
        ));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'nullable|in:pending,paid,refunded',
            'tracking_carrier' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
        ]);

        $order->status = $validated['status'];
        if (!empty($validated['payment_status'])) {
            $order->payment_status = $validated['payment_status'];
        }

        if (!empty($validated['tracking_carrier']) || !empty($validated['tracking_number'])) {
            $carrier = $validated['tracking_carrier'] ?? 'White Glove Logistics';
            $trNum = $validated['tracking_number'] ?? '';
            $order->notes = trim(($order->notes ? $order->notes . "\n" : '') . "Tracking: {$carrier} #{$trNum}");
        }

        $order->save();

        return redirect()->back()->with('success', "Order #{$order->order_number} status updated to " . strtoupper($order->status));
    }
}
