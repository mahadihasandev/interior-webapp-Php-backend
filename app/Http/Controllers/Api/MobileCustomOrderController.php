<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use App\Models\OrderTimelineEvent;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MobileCustomOrderController extends Controller
{
    /**
     * Live Spec & Dimension Estimator API for Mobile App.
     */
    public function calculateEstimate(Request $request): JsonResponse
    {
        $height = (float) $request->input('height', 96);
        $width = (float) $request->input('width', 72);
        $gauge = $request->input('gauge', '2.0mm');
        $finishMultiplier = (float) $request->input('finish_multiplier', 1.0);
        $addonTotal = (float) $request->input('addon_total', 0);

        $sqft = ($height * $width) / 144;
        $baseRate = match ($gauge) {
            '2.5mm' => 48,
            '2.0mm' => 38,
            default => 32,
        };

        $baseCost = $sqft * $baseRate * $finishMultiplier;
        $estimatedTotal = round($baseCost + $addonTotal + 450);
        $suggestedAdvance = round($estimatedTotal * 0.40); // 40%

        return response()->json([
            'success' => true,
            'data' => [
                'area_sqft' => round($sqft, 2),
                'estimated_total' => $estimatedTotal,
                'suggested_advance' => $suggestedAdvance,
                'currency' => 'USD',
            ],
        ]);
    }

    /**
     * Submit Custom Order inquiry from Mobile App.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'dimensions' => ['required', 'array'],
            'material_specs' => ['required', 'array'],
            'color_finish' => ['required', 'string'],
            'addon_features' => ['nullable', 'array'],
            'customer_notes' => ['nullable', 'string'],
            'vendor_id' => ['nullable', 'exists:vendors,id'],
        ]);

        // Default to first active vendor if unassigned
        $vendorId = $validated['vendor_id'] ?? Vendor::first()?->id;

        $order = DB::transaction(function () use ($validated, $vendorId, $request) {
            $orderNumber = 'CUST-' . strtoupper(date('Y')) . '-' . strtoupper(Str::random(6));

            $customOrder = CustomOrder::create([
                'order_number' => $orderNumber,
                'customer_id' => $request->user()?->id ?? 1, // Fallback demo user
                'vendor_id' => $vendorId,
                'title' => $validated['title'],
                'dimensions' => $validated['dimensions'],
                'material_specs' => $validated['material_specs'],
                'color_finish' => $validated['color_finish'],
                'addon_features' => $validated['addon_features'] ?? [],
                'customer_notes' => $validated['customer_notes'] ?? null,
                'current_stage' => 'order_placed',
                'status' => 'pending_review',
            ]);

            OrderTimelineEvent::create([
                'custom_order_id' => $customOrder->id,
                'stage' => 'order_placed',
                'title' => 'Custom Fitting Specification Submitted',
                'description' => 'Customer submitted custom requirements. Engineering team assessing structural feasibility.',
                'updated_by' => $request->user()?->id,
            ]);

            return $customOrder;
        });

        return response()->json([
            'success' => true,
            'message' => 'Custom order submitted for vendor feasibility review.',
            'data' => $order->load('timelineEvents'),
        ], 201);
    }

    /**
     * Fetch real-time visual timeline and progress status.
     */
    public function timeline(CustomOrder $order): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $order->load(['timelineEvents', 'vendor', 'product']),
        ]);
    }

    /**
     * Fake Advance Payment Simulation Endpoint for Client Demonstration.
     */
    public function fakePayAdvance(CustomOrder $order): JsonResponse
    {
        DB::transaction(function () use ($order) {
            $order->update([
                'advance_paid_at' => now(),
                'status' => 'in_production',
                'current_stage' => 'cutting_welding',
            ]);

            OrderTimelineEvent::create([
                'custom_order_id' => $order->id,
                'stage' => 'cutting_welding',
                'title' => 'Client Advance Payment Verified (Demo Mode)',
                'description' => "Advance deposit of \${$order->advance_amount_required} successfully recorded. Raw material cut-sheets generated.",
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Advance payment verified. Production stage advanced to cutting & welding.',
            'data' => $order->fresh(['timelineEvents']),
        ]);
    }
}
