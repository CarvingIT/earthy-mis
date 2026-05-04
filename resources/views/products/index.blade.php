<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Product operations</p>
                <h2 class="text-3xl font-extrabold leading-tight text-slate-900">Products</h2>
            </div>
            <p class="max-w-xl text-sm font-medium text-slate-500">Manage product catalog details, units, pricing, and descriptions from one organized workspace.</p>
        </div>
    </x-slot>

    <style>
        * { box-sizing: border-box; }

        .products-shell {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f8f4 48%, #f8fafc 100%);
        }

        .product-panel,
        .product-stat,
        .product-table-card {
            border: 1px solid rgba(15, 23, 42, .08);
            background: #ffffff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
        }

        .product-stat {
            position: relative;
            overflow: hidden;
            transition: box-shadow .18s ease, border-color .18s ease;
        }

        .product-stat::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--stat-tint), transparent 50%);
            opacity: .95;
            pointer-events: none;
        }

        .product-stat::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--stat-accent);
        }

        .product-stat:hover,
        .product-table-card:hover {
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

        .products-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .products-table {
            border-collapse: separate !important;
            border-spacing: 0;
        }

        .products-table thead th {
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

        .products-table tbody td {
            border-bottom: 1px solid rgba(15, 23, 42, .06);
            color: #334155;
            font-size: .875rem;
            padding: 1rem !important;
            vertical-align: top;
        }

        .products-table tbody tr {
            transition: background-color .16s ease;
        }

        .products-table tbody tr:hover {
            background: #f8fafc;
        }

        .products-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .product-table-card .dt-container {
            color: #475569;
            font-size: .875rem;
        }

        .product-table-card .dt-layout-row {
            align-items: center;
            gap: 1rem;
            margin: 0 0 1rem;
        }

        .product-table-card .dt-layout-row:last-child {
            margin: 1rem 0 0;
        }

        .product-table-card .dt-search label,
        .product-table-card .dt-info {
            color: #64748b;
            font-size: .82rem;
            font-weight: 700;
        }

        .product-table-card .dt-input {
            border: 1px solid #e2e8f0 !important;
            border-radius: .75rem !important;
            color: #334155;
            font-weight: 600;
            min-height: 2.65rem;
            outline: none;
            padding: .55rem .85rem !important;
            transition: border-color .16s ease, box-shadow .16s ease;
        }

        .product-table-card .dt-input:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, .14);
        }

        .product-table-card .dt-paging .dt-paging-button {
            border: 1px solid #e2e8f0 !important;
            border-radius: .65rem !important;
            color: #475569 !important;
            font-weight: 800;
            margin-left: .25rem;
            padding: .45rem .75rem !important;
        }

        .product-table-card .dt-paging .dt-paging-button.current,
        .product-table-card .dt-paging .dt-paging-button:hover {
            background: #0f172a !important;
            color: #ffffff !important;
        }

        @media (max-width: 640px) {
            .product-table-card .dt-layout-row {
                align-items: stretch;
                flex-direction: column;
            }

            .product-table-card .dt-search,
            .product-table-card .dt-search input {
                width: 100%;
            }
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
        $totalProducts = $products->count();
        $pricedProducts = $products->filter(fn ($product) => (float) $product->price > 0)->count();
        $averagePrice = $totalProducts > 0 ? $products->avg(fn ($product) => (float) $product->price) : 0;
        $unitCoverage = $totalProducts > 0
            ? round(($products->filter(fn ($product) => filled($product->base_unit_id))->count() / $totalProducts) * 100)
            : 0;
        $descriptionCoverage = $totalProducts > 0
            ? round(($products->filter(fn ($product) => filled($product->description))->count() / $totalProducts) * 100)
            : 0;

        $stats = [
            [
                'label' => 'Total Products',
                'value' => number_format($totalProducts),
                'note' => 'Catalog items',
                'icon' => 'M12 3l7 4v10l-7 4-7-4V7l7-4zM12 12l7-4M12 12v9M12 12L5 8',
                'style' => '--stat-accent: linear-gradient(135deg, #0284c7, #22d3ee); --stat-tint: rgba(14, 165, 233, .15); --stat-shadow: rgba(14, 165, 233, .3); --stat-text: #0369a1;',
            ],
            [
                'label' => 'Priced Items',
                'value' => number_format($pricedProducts),
                'note' => 'With price above zero',
                'icon' => 'M12 8c-2 0-3 .9-3 2s1 2 3 2 3 .9 3 2-1 2-3 2m0-8V6m0 12v-2M5 12a7 7 0 1114 0 7 7 0 01-14 0z',
                'style' => '--stat-accent: linear-gradient(135deg, #059669, #84cc16); --stat-tint: rgba(16, 185, 129, .16); --stat-shadow: rgba(16, 185, 129, .3); --stat-text: #047857;',
            ],
            [
                'label' => 'Average Price',
                'value' => 'Rs. ' . number_format((float) $averagePrice, 2),
                'note' => 'Across all products',
                'icon' => 'M4 19V5m5 14V9m5 10v-7m5 7V7',
                'style' => '--stat-accent: linear-gradient(135deg, #4f46e5, #06b6d4); --stat-tint: rgba(99, 102, 241, .14); --stat-shadow: rgba(79, 70, 229, .28); --stat-text: #4338ca;',
            ],
            [
                'label' => 'Unit Coverage',
                'value' => $unitCoverage . '%',
                'note' => 'With base unit set',
                'icon' => 'M4 7h16M4 12h16M4 17h16',
                'style' => '--stat-accent: linear-gradient(135deg, #f59e0b, #f97316); --stat-tint: rgba(245, 158, 11, .16); --stat-shadow: rgba(245, 158, 11, .3); --stat-text: #b45309;',
            ],
        ];
    @endphp

    <div class="products-shell min-h-screen py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="product-panel reveal rounded-2xl p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l7 4v10l-7 4-7-4V7l7-4zM12 12l7-4M12 12v9M12 12L5 8"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Product catalog</h3>
                            <p class="text-sm font-medium text-slate-500">Use search and sorting to find product pricing, units, and descriptions quickly.</p>
                        </div>
                    </div>

                    <a href="{{ route('products.create') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/15 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200 sm:w-auto">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                        </svg>
                        Add Product
                    </a>
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
                    <article class="product-stat reveal rounded-2xl p-5" style="{{ $stat['style'] }} --reveal-delay: {{ $index * 70 }}ms;">
                        <div class="relative z-10">
                            <div class="mb-5 flex items-start justify-between gap-3">
                                <div class="stat-icon flex h-12 w-12 items-center justify-center rounded-xl text-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-sm font-bold text-slate-500">{{ $stat['label'] }}</p>
                            <p class="stat-value mt-2 text-3xl font-black">{{ $stat['value'] }}</p>
                            <p class="mt-3 text-xs font-semibold leading-5 text-slate-500">{{ $stat['note'] }}</p>
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="product-table-card reveal rounded-2xl p-4 sm:p-6">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">All products</h3>
                        <p class="mt-1 text-sm font-medium text-slate-500">Catalog identity, unit setup, pricing, and descriptions.</p>
                    </div>
                    <span class="w-fit rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-emerald-700">
                        {{ number_format($totalProducts) }} records
                    </span>
                </div>

                <div class="products-table-wrap">
                    <table id="products-table" data-datatable class="products-table min-w-full">
                        <thead>
                            <tr>
                                <th>Name</th>
                                {{-- <th>SKU</th> --}}
                                <th>Base Unit</th>
                                <th>Price in Rs.</th>
                                <th>Created</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr>
                                    <td>
                                        <div class="flex items-start gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-sm font-black text-slate-700">
                                                {{ mb_strtoupper(mb_substr($product->name ?? 'P', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-extrabold text-slate-900">{{ $product->name }}</p>
                                                @if (filled($product->description))
                                                    <p class="mt-1 max-w-xs text-xs font-medium leading-5 text-slate-500">{{ $product->description }}</p>
                                                @else
                                                    <p class="mt-1 text-xs font-medium text-slate-400">Description not set</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    {{-- <td class="font-semibold text-slate-600">{{ $product->sku ?: 'Not set' }}</td> --}}
                                    <td>
                                        <span class="inline-flex rounded-full bg-sky-50 px-3 py-1 text-xs font-extrabold text-sky-700">
                                            {{ $product->baseUnit->name ?? 'Not set' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-extrabold text-slate-900">{{ number_format((float) $product->price, 2) }}</span>
                                    </td>
                                    <td class="font-semibold text-slate-600">{{ $product->created_at?->format('d M Y') ?: 'Not set' }}</td>
                                    <td>
                                        <div class="flex justify-end gap-2">
                                            <a class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 transition hover:bg-emerald-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-emerald-100" href="{{ route('products.edit', $product) }}" title="Edit product" aria-label="Edit {{ $product->name }}">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('products.destroy', $product) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-700 transition hover:bg-rose-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-rose-100" onclick="return confirm('Delete this product?')" title="Delete product" aria-label="Delete {{ $product->name }}">
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
                                    <td colspan="6">
                                        <div class="flex flex-col items-center justify-center py-12 text-center">
                                            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l7 4v10l-7 4-7-4V7l7-4zM12 12l7-4M12 12v9M12 12L5 8"/>
                                                </svg>
                                            </div>
                                            <p class="text-base font-extrabold text-slate-900">No products found</p>
                                            <p class="mt-1 text-sm font-medium text-slate-500">Create the first product to begin building the catalog.</p>
                                            <a href="{{ route('products.create') }}" class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/15 transition hover:bg-emerald-700">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                                                </svg>
                                                Add Product
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
            });
        </script>
    @endpush
</x-app-layout>
