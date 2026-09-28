@extends('layouts.seller')

@section('content')
<div class="space-y-6" x-data="{ 
    updateModalOpen: false, 
    activeOrder: null,
    orderStatus: '',
    paymentStatus: '',
    trackingCarrier: '',
    trackingNumber: '',
    openStatusModal(order) {
        this.activeOrder = order;
        this.orderStatus = order.status;
        this.paymentStatus = order.payment_status;
        this.trackingCarrier = 'White Glove Freight Express';
        this.trackingNumber = '';
        this.updateModalOpen = true;
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-stone-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-amber-900 mb-1">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>E-Commerce Fulfillment Center</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900 tracking-tight">
                Ready-Made Product Sales & Orders
            </h1>
            <p class="text-xs sm:text-sm text-stone-600 mt-1 max-w-2xl">
                Track customer sales of ready-made catalog editions (lounge chairs, platform beds, chandeliers, dining tables). Manage shipments and order fulfillment.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('seller.products.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold uppercase tracking-wider transition-colors shadow-xs">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Add New Product</span>
            </a>
            <a href="{{ route('seller.products.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-stone-300 hover:bg-stone-50 text-stone-800 text-xs font-bold uppercase tracking-wider transition-colors">
                <svg class="w-4 h-4 text-stone-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                <span>Product Catalog</span>
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 font-bold text-xs">✕</button>
        </div>
    @endif

    <!-- Metric KPI Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-2xs">
            <div class="flex items-center justify-between text-xs font-bold text-stone-500 uppercase tracking-wider">
                <span>Total Ready-Made Sales</span>
                <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-800">💰</span>
            </div>
            <div class="mt-3 text-2xl font-serif font-bold text-stone-900">
                ${{ number_format($totalRevenue, 2) }}
            </div>
            <div class="text-[11px] text-stone-500 mt-1">
                Confirmed purchases from web storefront
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-2xs">
            <div class="flex items-center justify-between text-xs font-bold text-stone-500 uppercase tracking-wider">
                <span>Total Units Sold</span>
                <span class="p-1.5 rounded-lg bg-blue-100 text-blue-800">📦</span>
            </div>
            <div class="mt-3 text-2xl font-serif font-bold text-stone-900">
                {{ $totalUnits }} Units
            </div>
            <div class="text-[11px] text-stone-500 mt-1">
                Across living, bedroom & lighting
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-2xs">
            <div class="flex items-center justify-between text-xs font-bold text-stone-500 uppercase tracking-wider">
                <span>Processing / Dispatch</span>
                <span class="p-1.5 rounded-lg bg-amber-100 text-amber-800">⚙️</span>
            </div>
            <div class="mt-3 text-2xl font-serif font-bold text-stone-900">
                {{ $processingCount + $pendingCount }} Orders
            </div>
            <div class="text-[11px] text-stone-500 mt-1">
                {{ $pendingCount }} awaiting payment, {{ $processingCount }} in prep
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-2xs">
            <div class="flex items-center justify-between text-xs font-bold text-stone-500 uppercase tracking-wider">
                <span>In Transit & Delivered</span>
                <span class="p-1.5 rounded-lg bg-stone-100 text-stone-800">🚚</span>
            </div>
            <div class="mt-3 text-2xl font-serif font-bold text-stone-900">
                {{ $shippedCount + $deliveredCount }} Orders
            </div>
            <div class="text-[11px] text-stone-500 mt-1">
                {{ $shippedCount }} on route, {{ $deliveredCount }} completed
            </div>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="bg-white p-4 rounded-2xl border border-stone-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Status Tab Pills -->
        <div class="flex flex-wrap items-center gap-1.5">
            @php
                $tabs = [
                    'all' => 'All Orders (' . $orders->total() . ')',
                    'pending' => 'Pending (' . $pendingCount . ')',
                    'processing' => 'Processing (' . $processingCount . ')',
                    'shipped' => 'Shipped (' . $shippedCount . ')',
                    'delivered' => 'Delivered (' . $deliveredCount . ')',
                ];
            @endphp
            @foreach($tabs as $key => $label)
                <a href="{{ route('seller.orders.ready_made', ['status' => $key, 'search' => $search]) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors {{ $statusFilter === $key ? 'bg-stone-900 text-white shadow-xs' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- Search input -->
        <form method="GET" action="{{ route('seller.orders.ready_made') }}" class="flex items-center gap-2">
            <input type="hidden" name="status" value="{{ $statusFilter }}">
            <div class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search order #, customer, city..." class="w-full pl-9 pr-3 py-1.5 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-stone-900">
                <svg class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <button type="submit" class="px-3 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold rounded-xl border border-stone-300">
                Filter
            </button>
            @if(!empty($search))
                <a href="{{ route('seller.orders.ready_made', ['status' => $statusFilter]) }}" class="text-xs text-rose-600 hover:underline">Clear</a>
            @endif
        </form>
    </div>

    <!-- Ready-Made Sales Orders Table -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-stone-200 bg-stone-50 text-[11px] font-bold text-stone-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Order Reference</th>
                        <th class="py-3.5 px-4">Customer & Destination</th>
                        <th class="py-3.5 px-4">Purchased Items</th>
                        <th class="py-3.5 px-4">Total Amount</th>
                        <th class="py-3.5 px-4">Payment</th>
                        <th class="py-3.5 px-4">Fulfillment Stage</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-xs">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-stone-50/70 transition-colors">
                            <!-- Order Reference -->
                            <td class="py-4 px-4 align-top">
                                <div class="font-mono font-bold text-stone-900 text-sm">
                                    {{ $ord->order_number }}
                                </div>
                                <div class="text-[11px] text-stone-500 mt-0.5">
                                    {{ $ord->created_at->format('M d, Y · h:i A') }}
                                </div>
                                <div class="text-[10px] text-stone-400 mt-1">
                                    {{ $ord->created_at->diffForHumans() }}
                                </div>
                            </td>

                            <!-- Customer & Destination -->
                            <td class="py-4 px-4 align-top">
                                <div class="font-bold text-stone-900">{{ $ord->customer_name }}</div>
                                <div class="text-stone-600 text-[11px] mt-0.5">{{ $ord->customer_email }}</div>
                                <div class="text-stone-500 text-[11px]">{{ $ord->customer_phone }}</div>
                                <div class="mt-1.5 flex items-start gap-1 text-[11px] text-stone-600 max-w-xs">
                                    <svg class="w-3.5 h-3.5 text-stone-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span>{{ $ord->shipping_address }}, {{ $ord->city }} ({{ $ord->postal_code }})</span>
                                </div>
                            </td>

                            <!-- Purchased Ready-Made Items -->
                            <td class="py-4 px-4 align-top min-w-[280px]">
                                <div class="space-y-2.5">
                                    @foreach($ord->items as $item)
                                        <div class="flex items-center gap-3">
                                            @if($item->product && $item->product->image_url)
                                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}" class="w-11 h-11 rounded-lg object-cover border border-stone-200 shrink-0">
                                            @else
                                                <div class="w-11 h-11 rounded-lg bg-stone-100 border border-stone-200 flex items-center justify-center text-stone-400 text-xs shrink-0">
                                                    🪑
                                                </div>
                                            @endif
                                            <div class="truncate">
                                                <div class="font-bold text-stone-900 text-xs truncate max-w-[220px]" title="{{ $item->product_name }}">
                                                    {{ $item->product_name }}
                                                </div>
                                                <div class="text-[11px] text-stone-500 mt-0.5">
                                                    Qty: <span class="font-bold text-stone-800">{{ $item->quantity }}</span> × ${{ number_format($item->price, 2) }}
                                                    <span class="text-stone-400">·</span>
                                                    <span class="font-semibold text-stone-900">${{ number_format($item->subtotal, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Total Amount -->
                            <td class="py-4 px-4 align-top">
                                <div class="font-bold text-stone-900 text-sm">
                                    ${{ number_format($ord->total_amount, 2) }}
                                </div>
                                <div class="text-[11px] text-stone-500 mt-0.5">
                                    Subtotal: ${{ number_format($ord->subtotal, 2) }}
                                </div>
                                <div class="text-[10px] text-stone-500">
                                    Shipping: {{ $ord->shipping_fee > 0 ? '$' . number_format($ord->shipping_fee, 2) : 'Free Freight' }}
                                </div>
                            </td>

                            <!-- Payment Status -->
                            <td class="py-4 px-4 align-top">
                                @if($ord->payment_status === 'paid')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        PAID ({{ strtoupper($ord->payment_method) }})
                                    </span>
                                @elseif($ord->payment_status === 'refunded')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-900 border border-rose-300">
                                        REFUNDED
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                        PENDING
                                    </span>
                                @endif
                            </td>

                            <!-- Fulfillment Stage -->
                            <td class="py-4 px-4 align-top">
                                @php
                                    $stageStyles = [
                                        'pending' => 'bg-amber-50 text-amber-900 border-amber-300',
                                        'processing' => 'bg-blue-50 text-blue-900 border-blue-300',
                                        'shipped' => 'bg-indigo-50 text-indigo-900 border-indigo-300',
                                        'delivered' => 'bg-emerald-50 text-emerald-900 border-emerald-300',
                                        'cancelled' => 'bg-stone-100 text-stone-700 border-stone-300',
                                    ];
                                    $style = $stageStyles[$ord->status] ?? 'bg-stone-100 text-stone-800 border-stone-300';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border uppercase tracking-wide {{ $style }}">
                                    {{ $ord->status }}
                                </span>

                                @if($ord->notes)
                                    <p class="text-[10px] text-stone-500 mt-1 max-w-[180px] line-clamp-2" title="{{ $ord->notes }}">
                                        📝 {{ $ord->notes }}
                                    </p>
                                @endif
                            </td>

                            <!-- Action button -->
                            <td class="py-4 px-4 align-top text-right">
                                <button type="button" 
                                        @click="openStatusModal({{ json_encode($ord) }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-stone-900 hover:bg-stone-800 text-white rounded-xl text-xs font-bold transition-all shadow-2xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    <span>Update Stage</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-stone-500">
                                <div class="text-3xl mb-2">📦</div>
                                <p class="text-sm font-semibold text-stone-800">No ready-made orders found</p>
                                <p class="text-xs text-stone-500 mt-1">Try changing filter status or clear search query.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-stone-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- UPDATE ORDER FULFILLMENT MODAL -->
    <div x-show="updateModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-950/70 backdrop-blur-xs">
        <div @click.away="updateModalOpen = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-stone-200 space-y-5 animate-in zoom-in-95">
            <div class="flex items-center justify-between pb-3 border-b border-stone-200">
                <div>
                    <span class="text-[10px] font-bold text-amber-800 uppercase tracking-widest">Order Fulfillment Stage</span>
                    <h3 class="text-lg font-serif font-bold text-stone-900" x-text="'Update Order #' + (activeOrder?.order_number || '')"></h3>
                </div>
                <button type="button" @click="updateModalOpen = false" class="text-stone-400 hover:text-stone-900 text-lg">✕</button>
            </div>

            <!-- Update Form -->
            <form :action="'/seller/orders/ready-made/' + activeOrder?.id + '/status'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Fulfillment Status</label>
                    <select name="status" x-model="orderStatus" class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900">
                        <option value="pending">Pending (Awaiting fulfillment)</option>
                        <option value="processing">Processing (Preparing & Packing)</option>
                        <option value="shipped">Shipped (Dispatched with Freight Carrier)</option>
                        <option value="delivered">Delivered (Completed Delivery)</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Payment Status</label>
                    <select name="payment_status" x-model="paymentStatus" class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900">
                        <option value="paid">PAID (Payment settled)</option>
                        <option value="pending">PENDING (Awaiting wire / approval)</option>
                        <option value="refunded">REFUNDED</option>
                    </select>
                </div>

                <div x-show="orderStatus === 'shipped'" class="space-y-3 p-3.5 bg-stone-50 rounded-xl border border-stone-200">
                    <div>
                        <label class="block text-[11px] font-bold text-stone-700 mb-1">Freight Carrier</label>
                        <input type="text" name="tracking_carrier" x-model="trackingCarrier" placeholder="e.g. White Glove Logistics, FedEx Freight" class="w-full px-3 py-1.5 text-xs bg-white border border-stone-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-stone-700 mb-1">Tracking Number / Waybill</label>
                        <input type="text" name="tracking_number" x-model="trackingNumber" placeholder="e.g. WGE-991208" class="w-full px-3 py-1.5 text-xs bg-white border border-stone-300 rounded-lg">
                    </div>
                </div>

                <div class="pt-3 border-t border-stone-200 flex items-center justify-end gap-3">
                    <button type="button" @click="updateModalOpen = false" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold rounded-xl">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold rounded-xl shadow-xs">
                        Save Order Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
