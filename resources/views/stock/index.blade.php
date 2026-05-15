<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Stock operations</p>
                <h2 class="text-3xl font-extrabold leading-tight text-slate-900">Stock Management</h2>
            </div>
            <p class="max-w-xl text-sm font-medium text-slate-500">Track inventory levels, manage stock transactions, and monitor product availability in bags.</p>
        </div>
    </x-slot>

    <style>
        * { box-sizing: border-box; }

        .stock-shell {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f8f4 48%, #f8fafc 100%);
        }

        .stock-panel,
        .stock-stat,
        .stock-table-card {
            border: 1px solid rgba(15, 23, 42, .08);
            background: #ffffff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
        }

        .stock-stat {
            position: relative;
            overflow: hidden;
            transition: box-shadow .18s ease, border-color .18s ease;
        }

        .stock-stat::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--stat-tint), transparent 50%);
            opacity: .95;
            pointer-events: none;
        }

        .stock-stat::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--stat-accent);
        }

        .stock-stat:hover,
        .stock-table-card:hover {
            box-shadow: 0 14px 34px rgba(15, 23, 42, .1);
        }

        .stat-icon {
            background: var(--stat-accent);
            box-shadow: 0 12px 28px var(--stat-shadow);
        }

        .stat-value {
            color: var(--stat-text);
            letter-spacing: 0;
        }

        .reveal {
            opacity: 0;
            transform: translate3d(0, 18px, 0);
            transition:
                opacity .48s ease,
                transform .48s ease,
                box-shadow .18s ease,
                border-color .18s ease;
            transition-delay: var(--reveal-delay, 0ms);
            will-change: opacity, transform;
        }

        .reveal.is-visible {
            opacity: 1;
            transform: translate3d(0, 0, 0);
            will-change: auto;
        }

        .stock-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .stock-table {
            border-collapse: separate !important;
            border-spacing: 0;
        }

        .stock-table thead th {
            border-bottom: 1px solid rgba(15, 23, 42, .08) !important;
            background: #f8fafc !important;
            color: #64748b !important;
            font-size: .72rem;
            font-weight: 800 !important;
            letter-spacing: .08em;
            padding: .9rem 1rem !important;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .stock-table tbody td {
            border-bottom: 1px solid rgba(15, 23, 42, .06);
            color: #334155;
            font-size: .875rem;
            padding: 1rem !important;
            vertical-align: top;
        }

        .stock-table tbody tr {
            transition: background-color .16s ease;
        }

        .stock-table tbody tr:hover {
            background: #f8fafc;
        }

        .stock-table tbody tr:last-child td {
            border-bottom: 0;
        }

        @media (prefers-reduced-motion: reduce) {
            .reveal {
                opacity: 1;
                transform: none;
                transition: none;
            }
        }
    </style>

    @php
        $totalTransactions = $stocks->count();
        $totalProducts = $currentStocks->count();
        $totalBags = $currentStocks->sum();
        $positiveTransactions = $stocks->filter(fn ($stock) => $stock->quantity > 0)->count();
        
        // Calculate total in sales units (using average conversion factor)
        $totalInSalesUnits = 0;
        if ($totalProducts > 0) {
            foreach ($currentStocks as $productId => $baseQuantity) {
                $product = $stocks->firstWhere('product_id', $productId)?->product;
                if ($product && $product->salesUnit) {
                    $conversionFactor = $product->salesUnit->related_unit_quantity ?? 1;
                    $totalInSalesUnits += round($baseQuantity / $conversionFactor);
                } else {
                    $totalInSalesUnits += round($baseQuantity); // Fallback to base unit
                }
            }
        }
        
        $stats = [
            [
                'label' => 'Current Products',
                'value' => number_format($totalProducts),
                'note' => 'Products with stock',
                'icon' => 'M12 3l7 4v10l-7 4-7-4V7l7-4zM12 12l7-4M12 12v9M12 12L5 8',
                'style' => '--stat-accent: linear-gradient(135deg, #0284c7, #22d3ee); --stat-tint: rgba(14, 165, 233, .15); --stat-shadow: rgba(14, 165, 233, .3); --stat-text: #0369a1;',
            ],
            [
                'label' => 'Total Stock',
                'value_base' => number_format(round($totalBags)),
                'value_sales' => number_format(round($totalInSalesUnits)),
                'note_base' => 'In base units (kg, ml, etc.)',
                'note_sales' => 'In sales units (bags, liters, etc.)',
                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                'style' => '--stat-accent: linear-gradient(135deg, #059669, #84cc16); --stat-tint: rgba(16, 185, 129, .16); --stat-shadow: rgba(16, 185, 129, .3); --stat-text: #047857;',
            ],
            [
                'label' => 'Stock Transactions',
                'value' => number_format($totalTransactions),
                'note' => 'All time records',
                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                'style' => '--stat-accent: linear-gradient(135deg, #4f46e5, #06b6d4); --stat-tint: rgba(99, 102, 241, .14); --stat-shadow: rgba(79, 70, 229, .28); --stat-text: #4338ca;',
            ],
            [
                'label' => 'Stock Additions',
                'value' => number_format($positiveTransactions),
                'note' => 'Positive transactions',
                'icon' => 'M12 4v16m8-8H4',
                'style' => '--stat-accent: linear-gradient(135deg, #f59e0b, #f97316); --stat-tint: rgba(245, 158, 11, .16); --stat-shadow: rgba(245, 158, 11, .3); --stat-text: #b45309;',
            ],
        ];
    @endphp

    <div class="stock-shell min-h-screen py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="stock-panel reveal rounded-2xl p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Stock transactions</h3>
                            <p class="text-sm font-medium text-slate-500">View all stock movements, current levels, and transaction history.</p>
                            <p class="mt-1 text-xs font-medium text-emerald-600">💡 Tip: Use "Sync Sales" if you added sales before setting up stock management.</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <!-- Sync Sales Button -->
                        <form method="POST" action="{{ route('stock.sync-sales') }}" onsubmit="return confirm('This will sync all historical sales with stock transactions. Continue?');">
                            @csrf
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-emerald-600/15 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200 sm:w-auto">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Sync Sales
                            </button>
                        </form>

                        <a href="{{ route('stock.create') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/15 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200 sm:w-auto">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                            </svg>
                            Add Stock
                        </a>
                    </div>
                </div>
            </section>

            @if (session('success'))
                <div class="reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800 shadow-sm" data-dismissible-alert>
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" class="-mr-1 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-emerald-700 transition hover:bg-emerald-100 focus:outline-none focus:ring-4 focus:ring-emerald-200" data-dismiss-alert aria-label="Dismiss alert">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($stats as $index => $stat)
                    <article class="stock-stat reveal rounded-2xl p-5" style="{{ $stat['style'] }} --reveal-delay: {{ $index * 70 }}ms;">
                        <div class="relative z-10">
                            <div class="mb-5 flex items-start justify-between gap-3">
                                <div class="stat-icon flex h-12 w-12 items-center justify-center rounded-xl text-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                                    </svg>
                                </div>
                                
                                @if(isset($stat['value_sales']))
                                    <!-- Toggle switch for unit type -->
                                    <div class="flex items-center gap-1 rounded-lg bg-slate-100 p-1">
                                        <button 
                                            type="button"
                                            id="btn-sales-unit" 
                                            class="unit-toggle-btn px-3 py-1 text-[10px] font-bold rounded-md transition-all bg-white text-emerald-700 shadow-sm"
                                            data-unit="sales"
                                        >
                                            Sales
                                        </button>
                                        <button 
                                            type="button"
                                            id="btn-base-unit" 
                                            class="unit-toggle-btn px-3 py-1 text-[10px] font-bold rounded-md transition-all text-slate-600 hover:text-slate-900"
                                            data-unit="base"
                                        >
                                            Base
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <p class="text-sm font-bold text-slate-500">{{ $stat['label'] }}</p>
                            
                            @if(isset($stat['value_sales']))
                                <!-- Stock stat with toggle -->
                                <p class="stat-value mt-2 text-3xl font-black" id="stock-total-value">{{ $stat['value_sales'] }}</p>
                                <p class="mt-3 text-xs font-semibold leading-5 text-slate-500" id="stock-total-note">{{ $stat['note_sales'] }}</p>
                            @else
                                <!-- Regular stat -->
                                <p class="stat-value mt-2 text-3xl font-black">{{ $stat['value'] }}</p>
                                <p class="mt-3 text-xs font-semibold leading-5 text-slate-500">{{ $stat['note'] }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="stock-table-card reveal rounded-2xl p-4 sm:p-6">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">All stock transactions</h3>
                        <p class="mt-1 text-sm font-medium text-slate-500">Complete history of stock additions, adjustments, and movements.</p>
                    </div>
                    <span class="w-fit rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-emerald-700">
                        {{ number_format($totalTransactions) }} records
                    </span>
                </div>

                <div class="stock-table-wrap">
                    <table id="stock-table" class="stock-table min-w-full">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Transaction Type</th>
                                <th>Quantity (Bags)</th>
                                <th>Cumulative Total</th>
                                <th>Notes</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stocks as $stock)
                                @php
                                    $key = "{$stock->product_id}_{$stock->id}";
                                    $cumulativeTotal = $cumulativeTotals[$key] ?? 0;
                                @endphp
                                <tr>
                                    <td class="font-semibold text-slate-600">{{ $stock->Date ? \Carbon\Carbon::parse($stock->Date)->format('d M Y') : 'Not set' }}</td>
                                    <td>
                                        <div class="flex items-start gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-sm font-black text-slate-700">
                                                {{ mb_strtoupper(mb_substr($stock->product->name ?? 'P', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-extrabold text-slate-900">{{ $stock->product->name ?? 'Unknown' }}</p>
                                                @if($stock->product->salesUnit && $stock->product->baseUnit)
                                                    @php
                                                        $conversionFactor = $stock->product->salesUnit->related_unit_quantity ?? 1;
                                                        $qtyInBase = abs($stock->quantity);
                                                        $qtyInSales = $qtyInBase / $conversionFactor;
                                                    @endphp
                                                    <p class="mt-1 text-xs font-medium text-slate-500">
                                                        {{ number_format(round($qtyInSales)) }} {{ $stock->product->salesUnit->name }} 
                                                        ({{ number_format(round($qtyInBase)) }} {{ $stock->product->baseUnit->name }})
                                                    </p>
                                                @else
                                                    <p class="mt-1 text-xs font-medium text-slate-500">{{ number_format(round(abs($stock->quantity))) }} {{ $stock->product->baseUnit->name ?? 'units' }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $typeColors = [
                                                'purchase' => 'bg-blue-50 text-blue-700',
                                                'adjustment' => 'bg-yellow-50 text-yellow-700',
                                                'return' => 'bg-purple-50 text-purple-700',
                                                'opening_stock' => 'bg-green-50 text-green-700',
                                            ];
                                            $typeClass = $typeColors[$stock->transaction_type ?? 'adjustment'] ?? 'bg-gray-50 text-gray-700';
                                        @endphp
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-extrabold {{ $typeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $stock->transaction_type ?? 'Adjustment')) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($stock->product->salesUnit && $stock->product->baseUnit)
                                            @php
                                                $conversionFactor = $stock->product->salesUnit->related_unit_quantity ?? 1;
                                                $qtyInBase = abs($stock->quantity);
                                                $qtyInSales = $qtyInBase / $conversionFactor;
                                                $sign = $stock->quantity > 0 ? '+' : '-';
                                            @endphp
                                            
                                            @if($stock->quantity > 0)
                                                <div class="space-y-1">
                                                    <span class="inline-flex items-center gap-1 font-extrabold text-emerald-700">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                        </svg>
                                                        {{ $sign }}{{ number_format(round($qtyInSales)) }} {{ $stock->product->salesUnit->name }}
                                                    </span>
                                                    <p class="text-xs font-semibold text-slate-500 pl-5">({{ $sign }}{{ number_format(round($qtyInBase)) }} {{ $stock->product->baseUnit->name }})</p>
                                                </div>
                                            @else
                                                <div class="space-y-1">
                                                    <span class="inline-flex items-center gap-1 font-extrabold text-rose-700">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                        </svg>
                                                        {{ $sign }}{{ number_format(round($qtyInSales)) }} {{ $stock->product->salesUnit->name }}
                                                    </span>
                                                    <p class="text-xs font-semibold text-slate-500 pl-5">({{ $sign }}{{ number_format(round($qtyInBase)) }} {{ $stock->product->baseUnit->name }})</p>
                                                </div>
                                            @endif
                                        @else
                                            @if($stock->quantity > 0)
                                                <span class="inline-flex items-center gap-1 font-extrabold text-emerald-700">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                    +{{ number_format(round($stock->quantity)) }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 font-extrabold text-rose-700">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                    </svg>
                                                    {{ number_format(round($stock->quantity)) }}
                                                </span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($stock->product->salesUnit && $stock->product->baseUnit)
                                            @php
                                                $conversionFactor = $stock->product->salesUnit->related_unit_quantity ?? 1;
                                                $cumulativeInSales = $cumulativeTotal / $conversionFactor;
                                            @endphp
                                            <div class="space-y-1">
                                                <span class="font-black text-slate-900">{{ number_format(round($cumulativeInSales)) }} {{ $stock->product->salesUnit->name }}</span>
                                                <p class="text-xs font-semibold text-slate-500">({{ number_format(round($cumulativeTotal)) }} {{ $stock->product->baseUnit->name }})</p>
                                            </div>
                                        @else
                                            <span class="font-black text-slate-900">{{ number_format(round($cumulativeTotal)) }} {{ $stock->product->baseUnit->name ?? 'units' }}</span>
                                        @endif
                                    </td>
                                    <td class="text-sm text-slate-600">{{ $stock->notes ?: '—' }}</td>
                                    <td>
                                        <div class="flex justify-end gap-2">
                                            <a class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 transition hover:bg-emerald-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-emerald-100" href="{{ route('stock.edit', $stock) }}" title="Edit transaction" aria-label="Edit stock transaction">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('stock.destroy', $stock) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-700 transition hover:bg-rose-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-rose-100" onclick="return confirm('Delete this stock transaction?')" title="Delete transaction" aria-label="Delete stock transaction">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.87 12.14A2 2 0 0116.14 21H7.86a2 2 0 01-1.99-1.86L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-8 0h10"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="flex flex-col items-center justify-center py-12 text-center">
                                            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                            <p class="text-base font-extrabold text-slate-900">No stock transactions found</p>
                                            <p class="mt-1 text-sm font-medium text-slate-500">Add your first stock transaction to begin tracking inventory.</p>
                                            <a href="{{ route('stock.create') }}" class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/15 transition hover:bg-emerald-700">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                                                </svg>
                                                Add Stock
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const revealItems = document.querySelectorAll('.reveal');
                document.querySelectorAll('[data-dismiss-alert]').forEach((button) => {
                    button.addEventListener('click', () => {
                        button.closest('[data-dismissible-alert]')?.remove();
                    });
                });

                if (!('IntersectionObserver' in window)) {
                    revealItems.forEach((item) => item.classList.add('is-visible'));
                    return;
                }

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting) return;

                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    });
                }, { threshold: .12 });

                revealItems.forEach((item) => observer.observe(item));
                
                // Stock unit toggle functionality
                const btnSalesUnit = document.getElementById('btn-sales-unit');
                const btnBaseUnit = document.getElementById('btn-base-unit');
                const stockTotalValue = document.getElementById('stock-total-value');
                const stockTotalNote = document.getElementById('stock-total-note');
                
                if (btnSalesUnit && btnBaseUnit) {
                    // Data attributes for both values
                    const baseValue = '{{ $stats[1]["value_base"] }}';
                    const salesValue = '{{ $stats[1]["value_sales"] }}';
                    const baseNote = '{{ $stats[1]["note_base"] }}';
                    const salesNote = '{{ $stats[1]["note_sales"] }}';
                    
                    // Default to sales units
                    setActiveUnit('sales');
                    
                    btnSalesUnit.addEventListener('click', function() {
                        setActiveUnit('sales');
                    });
                    
                    btnBaseUnit.addEventListener('click', function() {
                        setActiveUnit('base');
                    });
                    
                    function setActiveUnit(unitType) {
                        if (unitType === 'base') {
                            stockTotalValue.textContent = baseValue;
                            stockTotalNote.textContent = baseNote;
                            
                            // Update button styles
                            btnBaseUnit.classList.remove('text-slate-600', 'hover:text-slate-900');
                            btnBaseUnit.classList.add('bg-white', 'text-emerald-700', 'shadow-sm');
                            
                            btnSalesUnit.classList.remove('bg-white', 'text-emerald-700', 'shadow-sm');
                            btnSalesUnit.classList.add('text-slate-600', 'hover:text-slate-900');
                        } else {
                            stockTotalValue.textContent = salesValue;
                            stockTotalNote.textContent = salesNote;
                            
                            // Update button styles
                            btnSalesUnit.classList.remove('text-slate-600', 'hover:text-slate-900');
                            btnSalesUnit.classList.add('bg-white', 'text-emerald-700', 'shadow-sm');
                            
                            btnBaseUnit.classList.remove('bg-white', 'text-emerald-700', 'shadow-sm');
                            btnBaseUnit.classList.add('text-slate-600', 'hover:text-slate-900');
                        }
                    }
                }
            });
        </script>
    @endpush
</x-app-layout>
