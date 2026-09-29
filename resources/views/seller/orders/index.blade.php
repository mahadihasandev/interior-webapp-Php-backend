@extends('layouts.seller')

@section('content')
<div x-data="{
    quoteModalOpen: false,
    stageModalOpen: false,
    selectedOrder: null,
    quoteForm: {
        price: '',
        advance: '',
        days: 14,
        notes: ''
    },
    stageForm: {
        stage: 'cutting_welding',
        title: '',
        desc: ''
    },
    openQuoteModal(order) {
        this.selectedOrder = order;
        this.quoteForm.price = order.quoted_total_price || '';
        this.quoteForm.advance = order.advance_amount_required || (order.quoted_total_price ? Math.round(order.quoted_total_price * 0.4) : '');
        this.quoteForm.days = order.estimated_completion_days || 14;
        this.quoteForm.notes = order.seller_notes || '';
        this.quoteModalOpen = true;
    },
    openStageModal(order) {
        this.selectedOrder = order;
        this.stageForm.stage = order.current_stage || 'cutting_welding';
        this.stageForm.title = 'Production Milestone: ' + (order.stage_label || 'Stage Update');
        this.stageForm.desc = '';
        this.stageModalOpen = true;
    },
    calculateSuggestedAdvance() {
        let p = parseFloat(this.quoteForm.price) || 0;
        this.quoteForm.advance = Math.round(p * 0.4);
    },
    getAlloyHex(finishName) {
        if (!finishName) return '#C5A059';
        let f = finishName.toLowerCase();
        if (f.includes('gold')) return '#C5A059';
        if (f.includes('black')) return '#222222';
        if (f.includes('bronze')) return '#7E5B3D';
        if (f.includes('silver')) return '#B0B0B0';
        if (f.includes('copper') || f.includes('rose')) return '#B76E79';
        return '#44403c';
    },
    getStageStep(stage) {
        const map = {
            'raw_material_sourcing': 1,
            'cutting_welding': 2,
            'powder_coating': 3,
            'assembly_glass_fitting': 4,
            'quality_check': 5,
            'ready_for_dispatch': 6,
            'delivered': 7
        };
        return map[stage] || 2;
    }
}" class="space-y-10">

    <!-- Top Header & Metrics Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-stone-200 pb-6">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-stone-100 border border-stone-300 rounded-full text-xs font-bold text-stone-900 uppercase tracking-widest mb-2">
                <span>Multi-Vendor Workshop & CAD Studio Engine</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900 tracking-tight">
                Custom Architectural Fittings & Workshop Dashboard
            </h1>
            <p class="text-xs sm:text-sm text-stone-700 font-normal mt-1">
                Visual CAD evaluation, binding quotation with 40% advance escrow, and live 7-stage manufacturing telemetry.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3.5 py-2 rounded-xl bg-white border border-stone-300 text-xs font-bold text-stone-900 shadow-2xs">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 mr-2"></span>
                <span>{{ $metrics['total_pending'] }} Pending Review</span>
            </span>
            <span class="inline-flex items-center px-3.5 py-2 rounded-xl bg-white border border-stone-300 text-xs font-bold text-stone-900 shadow-2xs">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 mr-2"></span>
                <span>{{ $metrics['in_production'] }} in Workshop</span>
            </span>
        </div>
    </div>

    <!-- Custom Orders Management Area -->
    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-stone-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-stone-50/60">
            <div>
                <h2 class="text-lg font-serif font-bold text-stone-900">Live Custom Orders & Visual CAD Queue</h2>
                <p class="text-xs text-stone-600 font-medium">Real-time buyer specifications submitted via web visualizer and mobile app.</p>
            </div>

            <!-- Filter Tabs -->
            <div class="flex flex-wrap gap-2 text-xs">
                <a href="{{ route('seller.orders.index') }}" class="px-3.5 py-2 rounded-xl border {{ !request('status') ? 'bg-stone-900 text-white border-stone-900 font-bold' : 'bg-white text-stone-700 border-stone-300 hover:bg-stone-100 font-semibold' }}">
                    All Inquiries
                </a>
                <a href="{{ route('seller.orders.index', ['status' => 'pending_review']) }}" class="px-3.5 py-2 rounded-xl border {{ request('status') == 'pending_review' ? 'bg-stone-900 text-white border-stone-900 font-bold' : 'bg-white text-stone-700 border-stone-300 hover:bg-stone-100 font-semibold' }}">
                    Pending Review
                </a>
                <a href="{{ route('seller.orders.index', ['status' => 'in_production']) }}" class="px-3.5 py-2 rounded-xl border {{ request('status') == 'in_production' ? 'bg-stone-900 text-white border-stone-900 font-bold' : 'bg-white text-stone-700 border-stone-300 hover:bg-stone-100 font-semibold' }}">
                    In Production
                </a>
            </div>
        </div>

        @if($orders->isEmpty())
            <div class="p-16 text-center text-stone-600 space-y-3">
                <p class="font-serif text-lg font-bold text-stone-900">No custom orders found matching this filter.</p>
                <p class="text-xs">Custom orders created by clients via mobile or web will appear here in real-time.</p>
            </div>
        @else
            <!-- Desktop / Laptop Visual Table View (md, lg) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-100 text-stone-800 uppercase tracking-wider border-b border-stone-200 font-bold">
                        <tr>
                            <th class="px-6 py-4">Visual CAD Elevation & Ref</th>
                            <th class="px-6 py-4">Specification & Alloy Color</th>
                            <th class="px-6 py-4">Quoted Total & Advance</th>
                            <th class="px-6 py-4">7-Stage Visual Timeline</th>
                            <th class="px-6 py-4 text-right">Workshop Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 text-stone-900 font-medium">
                        @foreach($orders as $order)
                            @php
                                $finish = strtolower($order->color_finish ?? '');
                                $hex = '#C5A059';
                                if (str_contains($finish, 'black')) $hex = '#222222';
                                elseif (str_contains($finish, 'bronze')) $hex = '#7E5B3D';
                                elseif (str_contains($finish, 'silver')) $hex = '#A8A29E';
                                elseif (str_contains($finish, 'copper') || str_contains($finish, 'rose')) $hex = '#B76E79';

                                $stageMap = [
                                    'raw_material_sourcing' => 1,
                                    'cutting_welding' => 2,
                                    'powder_coating' => 3,
                                    'assembly_glass_fitting' => 4,
                                    'quality_check' => 5,
                                    'ready_for_dispatch' => 6,
                                    'delivered' => 7,
                                ];
                                $curStep = $stageMap[$order->current_stage ?? 'cutting_welding'] ?? 2;
                            @endphp
                            <tr class="hover:bg-stone-50 transition-colors">
                                <!-- Col 1: Visual CAD Elevation Frame + Reference -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <!-- Mini SVG CAD Frame Elevation -->
                                        <div class="w-14 h-16 rounded-xl bg-stone-100 border border-stone-300 p-1 flex items-center justify-center shrink-0 shadow-2xs relative">
                                            <svg viewBox="0 0 40 50" class="w-full h-full">
                                                <!-- Outer Frame Extrusion with Alloy Color -->
                                                <rect x="2" y="2" width="36" height="46" rx="2" fill="#fafaf9" stroke="{{ $hex }}" stroke-width="3" />
                                                <!-- Mullion Bar -->
                                                <line x1="2" y1="25" x2="38" y2="25" stroke="{{ $hex }}" stroke-width="2" />
                                                <!-- Fluted lines / glass representation -->
                                                <line x1="10" y1="5" x2="10" y2="22" stroke="{{ $hex }}" stroke-opacity="0.3" stroke-width="1" />
                                                <line x1="20" y1="5" x2="20" y2="22" stroke="{{ $hex }}" stroke-opacity="0.3" stroke-width="1" />
                                                <line x1="30" y1="5" x2="30" y2="22" stroke="{{ $hex }}" stroke-opacity="0.3" stroke-width="1" />
                                                <line x1="10" y1="28" x2="10" y2="45" stroke="{{ $hex }}" stroke-opacity="0.3" stroke-width="1" />
                                                <line x1="20" y1="28" x2="20" y2="45" stroke="{{ $hex }}" stroke-opacity="0.3" stroke-width="1" />
                                                <line x1="30" y1="28" x2="30" y2="45" stroke="{{ $hex }}" stroke-opacity="0.3" stroke-width="1" />
                                            </svg>
                                        </div>

                                        <div>
                                            <div class="font-mono font-bold text-stone-900 text-sm">{{ $order->order_number }}</div>
                                            <div class="text-stone-700 text-xs font-semibold mt-0.5">{{ $order->customer->name ?? 'Architect Client' }}</div>
                                            <div class="text-[11px] text-stone-500 font-mono">{{ $order->created_at->format('M d, Y') }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Col 2: Specification & Finish Swatch -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-stone-900 text-xs">{{ $order->title }}</div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <!-- Color Finish Swatch Pill -->
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-stone-100 border border-stone-300 text-[11px] font-bold text-stone-900">
                                            <span class="w-3 h-3 rounded-full border border-stone-400" style="background-color: {{ $hex }}"></span>
                                            <span>{{ $order->color_finish }}</span>
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-stone-700 mt-1 font-mono">
                                        {{ $order->dimensions['height'] ?? '96' }}{{ isset($order->dimensions['unit']) ? ' ' . $order->dimensions['unit'] : '"' }}H × {{ $order->dimensions['width'] ?? '72' }}{{ isset($order->dimensions['unit']) ? ' ' . $order->dimensions['unit'] : '"' }}W · {{ $order->material_specs['aluminum_profile'] ?? ($order->material_specs['profile_gauge'] ?? '2.0mm') }}
                                    </div>
                                </td>

                                <!-- Col 3: Quoted Total & Advance Status -->
                                <td class="px-6 py-4">
                                    @if($order->quoted_total_price)
                                        <div class="font-bold text-stone-900 text-sm font-mono">{{ number_format($order->quoted_total_price, 0) }} SAR</div>
                                        <div class="text-[11px] text-stone-700 mt-0.5">
                                            Req. Advance: <span class="font-bold text-stone-900 font-mono">{{ number_format($order->advance_amount_required, 0) }} SAR</span>
                                        </div>
                                        @if($order->advance_paid_at)
                                            <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-md bg-emerald-100 text-emerald-900 border border-emerald-300 mt-1">
                                                ✓ Advance Paid ({{ $order->advance_paid_at->format('M d') }})
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-md bg-stone-100 text-stone-900 border border-stone-300 mt-1">
                                                ○ Awaiting 40% Deposit
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-stone-500 font-semibold italic">Requires Feasibility Quote</span>
                                    @endif
                                </td>

                                <!-- Col 4: 7-Stage Visual Manufacturing Pipeline Meter -->
                                <td class="px-6 py-4">
                                    <div class="space-y-1.5 min-w-[170px]">
                                        <div class="flex justify-between items-center text-[10px] font-bold">
                                            <span class="text-stone-900 uppercase">Stage {{ $curStep }}/7: {{ $order->stage_label }}</span>
                                        </div>

                                        <!-- Visual 7-segment progress meter bar -->
                                        <div class="grid grid-cols-7 gap-1">
                                            @for($i = 1; $i <= 7; $i++)
                                                <div class="h-2 rounded-xs {{ $i <= $curStep ? 'bg-stone-900' : 'bg-stone-200' }}" title="Stage {{ $i }}"></div>
                                            @endfor
                                        </div>

                                        <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded
                                            @if($order->status === 'in_production') bg-emerald-100 text-emerald-900 border border-emerald-300
                                            @elseif($order->status === 'pending_review') bg-stone-100 text-stone-900 border border-stone-300
                                            @elseif($order->status === 'reviewed_quoted') bg-stone-100 text-stone-900 border border-stone-300
                                            @elseif($order->status === 'completed') bg-stone-100 text-stone-900 border border-stone-300
                                            @else bg-rose-100 text-rose-900 border border-rose-300 @endif
                                        ">
                                            {{ str_replace('_', ' ', $order->status) }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Col 5: Actions -->
                                <td class="px-6 py-4 text-right space-y-2">
                                    <!-- Quote Button -->
                                    <button
                                        @click="openQuoteModal({{ json_encode($order) }})"
                                        class="px-3.5 py-2 bg-stone-900 hover:bg-stone-800 text-white rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm transition-colors cursor-pointer"
                                    >
                                        {{ $order->quoted_total_price ? 'Update Quote' : 'Review & Quote' }}
                                    </button>

                                    <!-- Instant Fake Advance Payment Simulation (Client Demo Feature) -->
                                    @if($order->quoted_total_price && !$order->advance_paid_at)
                                        <form method="POST" action="{{ route('seller.orders.simulate_advance', $order->id) }}">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="px-3 py-1.5 bg-emerald-700 hover:bg-emerald-600 active:bg-emerald-800 text-white rounded-xl text-[11px] font-bold uppercase tracking-wider shadow-sm transition-colors block w-full mt-1.5 cursor-pointer"
                                                title="Instant Fake Payment for Client Demo"
                                            >
                                                💳 Simulate Fake Advance
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Advance Stage Button -->
                                    @if($order->advance_paid_at)
                                        <button
                                            @click="openStageModal({{ json_encode($order) }})"
                                            class="px-3 py-1.5 bg-stone-800 hover:bg-stone-900 text-white rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm transition-colors block w-full mt-1.5 cursor-pointer"
                                        >
                                            Next Stage →
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card Stack View (Smartphone sm) -->
            <div class="md:hidden divide-y divide-stone-200">
                @foreach($orders as $order)
                    @php
                        $finish = strtolower($order->color_finish ?? '');
                        $hex = '#C5A059';
                        if (str_contains($finish, 'black')) $hex = '#222222';
                        elseif (str_contains($finish, 'bronze')) $hex = '#7E5B3D';
                        elseif (str_contains($finish, 'silver')) $hex = '#A8A29E';
                        elseif (str_contains($finish, 'copper') || str_contains($finish, 'rose')) $hex = '#B76E79';
                    @endphp
                    <div class="p-5 space-y-3 bg-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-mono font-bold text-xs text-stone-900">{{ $order->order_number }}</span>
                                <h3 class="text-sm font-bold text-stone-900 mt-0.5">{{ $order->title }}</h3>
                            </div>
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-stone-100 text-stone-900 border border-stone-300">
                                {{ str_replace('_', ' ', $order->status) }}
                            </span>
                        </div>

                        <div class="bg-stone-50 p-4 rounded-xl border border-stone-200 text-xs space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full border border-stone-400" style="background-color: {{ $hex }}"></span>
                                <span class="font-bold text-stone-900">{{ $order->color_finish }}</span>
                            </div>
                            <p class="text-stone-700">Dimensions: {{ $order->dimensions['height'] ?? '96' }}{{ isset($order->dimensions['unit']) ? ' ' . $order->dimensions['unit'] : '"' }}H × {{ $order->dimensions['width'] ?? '72' }}{{ isset($order->dimensions['unit']) ? ' ' . $order->dimensions['unit'] : '"' }}W</p>
                            <p class="font-bold text-stone-900 pt-1 font-mono">
                                Total: {{ number_format($order->quoted_total_price ?? 0, 0) }} SAR
                                (Advance: {{ number_format($order->advance_amount_required ?? 0, 0) }} SAR)
                            </p>
                        </div>

                        <div class="flex flex-col gap-2 pt-1">
                            <button
                                @click="openQuoteModal({{ json_encode($order) }})"
                                class="w-full py-2.5 bg-stone-900 text-white rounded-xl text-xs font-bold uppercase tracking-wider"
                            >
                                {{ $order->quoted_total_price ? 'Update Quote' : 'Review & Quote' }}
                            </button>

                            @if($order->quoted_total_price && !$order->advance_paid_at)
                                <form method="POST" action="{{ route('seller.orders.simulate_advance', $order->id) }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="w-full py-2.5 bg-emerald-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider"
                                    >
                                        💳 Simulate Fake Advance
                                    </button>
                                </form>
                            @endif

                            @if($order->advance_paid_at)
                                <button
                                    @click="openStageModal({{ json_encode($order) }})"
                                    class="w-full py-2.5 bg-stone-800 text-white rounded-xl text-xs font-bold uppercase tracking-wider"
                                >
                                    Update Production Stage
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-stone-200 bg-stone-50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- Analytics Widget Section -->
    @include('seller.dashboard.analytics-widget')

    <!-- Alpine.js Modal 1: Quote & Feasibility Assessment with VISUAL CAD Elevation -->
    <div x-show="quoteModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div @click="quoteModalOpen = false" class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm"></div>

            <div class="relative w-full max-w-xl bg-white rounded-3xl border border-stone-200 p-5 sm:p-8 shadow-2xl space-y-6 text-stone-900 max-h-[90vh] overflow-y-auto my-auto">
                <div class="flex justify-between items-start border-b border-stone-200 pb-4">
                    <div>
                        <h3 class="text-xl font-serif font-bold text-stone-900">Custom Fitting Engineering Quote</h3>
                        <p class="text-xs text-stone-600 mt-0.5">Reference: <span class="font-mono font-bold text-stone-900" x-text="selectedOrder?.order_number"></span></p>
                    </div>
                    <button @click="quoteModalOpen = false" class="text-stone-500 hover:text-stone-900 text-2xl font-bold">×</button>
                </div>

                <!-- Visual CAD Elevation Specification Preview Inside Modal -->
                <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl flex items-center gap-4">
                    <div class="w-16 h-20 rounded-xl bg-white border border-stone-300 p-1 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 40 50" class="w-full h-full">
                            <rect x="2" y="2" width="36" height="46" rx="2" fill="#f5f5f4" :stroke="getAlloyHex(selectedOrder?.color_finish)" stroke-width="3" />
                            <line x1="2" y1="25" x2="38" y2="25" :stroke="getAlloyHex(selectedOrder?.color_finish)" stroke-width="2" />
                            <line x1="12" y1="5" x2="12" y2="45" :stroke="getAlloyHex(selectedOrder?.color_finish)" stroke-opacity="0.3" stroke-width="1" />
                            <line x1="28" y1="5" x2="28" y2="45" :stroke="getAlloyHex(selectedOrder?.color_finish)" stroke-opacity="0.3" stroke-width="1" />
                        </svg>
                    </div>
                    <div class="text-xs space-y-1">
                        <p class="font-bold text-stone-900 text-sm" x-text="selectedOrder?.title"></p>
                        <p class="text-stone-700">
                            Finish: <span class="font-bold text-stone-900" x-text="selectedOrder?.color_finish"></span>
                        </p>
                        <p class="text-stone-600 font-mono">
                            Wall Opening: <span x-text="selectedOrder?.dimensions?.height || 96"></span>"H × <span x-text="selectedOrder?.dimensions?.width || 72"></span>"W
                        </p>
                    </div>
                </div>

                <form :action="'/seller/orders/' + selectedOrder?.id + '/quote'" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-stone-800 mb-1">
                            Total Quoted Price (SAR / ريال) *
                        </label>
                        <input
                            type="number"
                            step="0.01"
                            name="quoted_total_price"
                            required
                            x-model="quoteForm.price"
                            @input="calculateSuggestedAdvance()"
                            placeholder="3450.00"
                            class="w-full px-4 py-3 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-sm font-bold focus:border-stone-900 focus:bg-white"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-stone-800 mb-1">
                                Required Advance (SAR / ريال) *
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                name="advance_amount_required"
                                required
                                x-model="quoteForm.advance"
                                placeholder="1380.00"
                                class="w-full px-4 py-3 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-sm font-bold focus:border-stone-900 focus:bg-white"
                            />
                            <p class="text-[10px] text-stone-600 mt-1 font-semibold">Standard: 40% escrow threshold</p>
                        </div>

                        <div>
                            <label class="block font-bold uppercase tracking-wider text-stone-800 mb-1">
                                Estimated Days *
                            </label>
                            <input
                                type="number"
                                name="estimated_completion_days"
                                required
                                x-model="quoteForm.days"
                                min="1"
                                max="180"
                                class="w-full px-4 py-3 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-sm font-bold focus:border-stone-900 focus:bg-white"
                            />
                            <p class="text-[10px] text-stone-600 mt-1 font-semibold">Manufacturing SLA</p>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold uppercase tracking-wider text-stone-800 mb-1">
                            Workshop Engineering Notes
                        </label>
                        <textarea
                            name="seller_notes"
                            rows="3"
                            x-model="quoteForm.notes"
                            placeholder="Include alloy extrusion batch number, tolerance certifications, crating schedule..."
                            class="w-full px-4 py-3 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs focus:border-stone-900 focus:bg-white"
                        ></textarea>
                    </div>

                    <div class="flex gap-3 pt-3">
                        <button
                            type="button"
                            @click="quoteModalOpen = false"
                            class="flex-1 py-3 bg-stone-100 hover:bg-stone-200 text-stone-800 rounded-xl font-bold uppercase tracking-wider transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="flex-2 py-3 bg-stone-900 hover:bg-stone-800 text-white rounded-xl font-bold uppercase tracking-wider shadow-md transition-colors cursor-pointer"
                        >
                            Issue Binding Quote to Client
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Alpine.js Modal 2: Production Stage Update with VISUAL Telemetry -->
    <div x-show="stageModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div @click="stageModalOpen = false" class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm"></div>

            <div class="relative w-full max-w-xl bg-white rounded-3xl border border-stone-200 p-5 sm:p-8 shadow-2xl space-y-6 text-stone-900 max-h-[90vh] overflow-y-auto my-auto">
                <div class="flex justify-between items-start border-b border-stone-200 pb-4">
                    <div>
                        <h3 class="text-xl font-serif font-bold text-stone-900">Advance Production Milestone</h3>
                        <p class="text-xs text-stone-600 mt-0.5">Order Ref: <span class="font-mono font-bold text-stone-900" x-text="selectedOrder?.order_number"></span></p>
                    </div>
                    <button @click="stageModalOpen = false" class="text-stone-500 hover:text-stone-900 text-2xl font-bold">×</button>
                </div>

                <form :action="'/seller/orders/' + selectedOrder?.id + '/stage'" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-stone-800 mb-1">
                            Select Current Production Stage (7-Stage Workflow) *
                        </label>
                        <select
                            name="stage"
                            required
                            x-model="stageForm.stage"
                            class="w-full px-4 py-3 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-sm font-bold focus:border-stone-900 focus:bg-white"
                        >
                            <option value="raw_material_sourcing">Stage 1: Raw Material Sourcing & Extrusion Inspection</option>
                            <option value="cutting_welding">Stage 2: Precision CNC Cutting & Structural Welding</option>
                            <option value="powder_coating">Stage 3: Electrostatic Powder Coating & Anodizing</option>
                            <option value="assembly_glass_fitting">Stage 4: Fluted Glass Fitting & Pivot Hardware</option>
                            <option value="quality_check">Stage 5: Master Engineering Tolerance QC</option>
                            <option value="ready_for_dispatch">Stage 6: Crated & Ready for White-Glove Dispatch</option>
                            <option value="delivered">Stage 7: Delivered & Mounted on Site</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold uppercase tracking-wider text-stone-800 mb-1">
                            Milestone Broadcast Title *
                        </label>
                        <input
                            type="text"
                            name="title"
                            required
                            x-model="stageForm.title"
                            placeholder="e.g. Aluminum Frames Anodized to Champagne Gold"
                            class="w-full px-4 py-3 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-sm font-medium focus:border-stone-900 focus:bg-white"
                        />
                    </div>

                    <div>
                        <label class="block font-bold uppercase tracking-wider text-stone-800 mb-1">
                            Inspection Notes & Tolerance Verification
                        </label>
                        <textarea
                            name="description"
                            rows="3"
                            x-model="stageForm.desc"
                            placeholder="Detail micron anodizing depth, glass tensile test, or packing crating serial number..."
                            class="w-full px-4 py-3 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs focus:border-stone-900 focus:bg-white"
                        ></textarea>
                    </div>

                    <div class="flex gap-3 pt-3">
                        <button
                            type="button"
                            @click="stageModalOpen = false"
                            class="flex-1 py-3 bg-stone-100 hover:bg-stone-200 text-stone-800 rounded-xl font-bold uppercase tracking-wider transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="flex-2 py-3 bg-stone-900 hover:bg-stone-800 text-white rounded-xl font-bold uppercase tracking-wider shadow-md transition-colors cursor-pointer"
                        >
                            Broadcast Milestone to Buyer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
