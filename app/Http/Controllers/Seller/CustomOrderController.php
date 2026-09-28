<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use App\Models\OrderTimelineEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CustomOrderController extends Controller
{
    /**
     * Display a listing of custom orders for the authenticated seller.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = CustomOrder::with(['customer', 'product', 'timelineEvents'])
            ->latest();

        // Multi-tenant vendor isolation
        if (!$user?->isSuperAdmin() && $user?->vendor_id) {
            $query->where('vendor_id', $user->vendor_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15);

        // Pre-compute aggregate metrics for sub-50ms dashboard performance
        $metrics = [
            'total_pending' => CustomOrder::where('status', 'pending_review')->count(),
            'in_production' => CustomOrder::where('status', 'in_production')->count(),
            'completed' => CustomOrder::where('status', 'completed')->count(),
        ];

        return view('seller.orders.index', compact('orders', 'metrics'));
    }

    /**
     * Seller quotes price, required advance payment, and estimated timeline.
     */
    public function quote(Request $request, CustomOrder $order)
    {
        $validated = $request->validate([
            'quoted_total_price' => ['required', 'numeric', 'min:1'],
            'advance_amount_required' => ['required', 'numeric', 'min:0', 'lte:quoted_total_price'],
            'estimated_completion_days' => ['required', 'integer', 'min:1', 'max:180'],
            'seller_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $order->update([
            'quoted_total_price' => $validated['quoted_total_price'],
            'advance_amount_required' => $validated['advance_amount_required'],
            'estimated_completion_days' => $validated['estimated_completion_days'],
            'seller_notes' => $validated['seller_notes'] ?? null,
            'status' => 'reviewed_quoted',
        ]);

        OrderTimelineEvent::create([
            'custom_order_id' => $order->id,
            'stage' => 'order_placed',
            'title' => 'Seller Quotation & Feasibility Provided',
            'description' => "Quoted \${$order->quoted_total_price} with required \${$order->advance_amount_required} advance. Estimated completion: {$order->estimated_completion_days} days.",
            'updated_by' => $request->user()?->id,
        ]);

        return back()->with('success', "Order #{$order->order_number} quotation submitted successfully.");
    }

    /**
     * Accept the custom order for production.
     */
    public function accept(Request $request, CustomOrder $order)
    {
        $order->update([
            'status' => 'accepted',
        ]);

        OrderTimelineEvent::create([
            'custom_order_id' => $order->id,
            'stage' => 'order_placed',
            'title' => 'Order Accepted by Vendor',
            'description' => 'Awaiting client advance payment to commence raw material reservation.',
            'updated_by' => $request->user()?->id,
        ]);

        return back()->with('success', "Order #{$order->order_number} accepted.");
    }

    /**
     * Reject custom order.
     */
    public function reject(Request $request, CustomOrder $order)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $order->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        OrderTimelineEvent::create([
            'custom_order_id' => $order->id,
            'stage' => 'order_placed',
            'title' => 'Order Rejected / Engineering Infeasible',
            'description' => $validated['rejection_reason'],
            'updated_by' => $request->user()?->id,
        ]);

        return back()->with('info', "Order #{$order->order_number} marked as rejected.");
    }

    /**
     * Simulate Fake Advance Payment for Client Demo.
     */
    public function simulateAdvancePayment(Request $request, CustomOrder $order)
    {
        DB::transaction(function () use ($order, $request) {
            $order->update([
                'advance_paid_at' => now(),
                'status' => 'in_production',
                'current_stage' => 'cutting_welding',
            ]);

            OrderTimelineEvent::create([
                'custom_order_id' => $order->id,
                'stage' => 'cutting_welding',
                'title' => 'Advance Payment Verified (Demo Mode)',
                'description' => "Client advance deposit of \${$order->advance_amount_required} verified. Production initiated.",
                'updated_by' => $request->user()?->id,
            ]);
        });

        return back()->with('success', "Advance payment recorded. Order #{$order->order_number} moved into production!");
    }

    /**
     * Update manufacturing stage and post progress evidence.
     */
    public function updateStage(Request $request, CustomOrder $order)
    {
        $validated = $request->validate([
            'stage' => ['required', 'string', 'in:' . implode(',', array_keys(CustomOrder::STAGES))],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'photo_evidence_url' => ['nullable', 'url'],
        ]);

        DB::transaction(function () use ($order, $validated, $request) {
            $isComplete = $validated['stage'] === 'delivered';
            $isReady = $validated['stage'] === 'ready_for_dispatch';

            $order->update([
                'current_stage' => $validated['stage'],
                'status' => $isComplete ? 'completed' : ($isReady ? 'ready' : 'in_production'),
            ]);

            OrderTimelineEvent::create([
                'custom_order_id' => $order->id,
                'stage' => $validated['stage'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'photo_evidence_url' => $validated['photo_evidence_url'] ?? null,
                'updated_by' => $request->user()?->id,
            ]);
        });

        return back()->with('success', "Production stage advanced to {$order->stage_label}.");
    }
}
