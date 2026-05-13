<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Sales operations</p>
                <h2 class="text-3xl font-extrabold leading-tight text-slate-900">Sales Management</h2>
            </div>
            <p class="max-w-xl text-sm font-medium text-slate-500">Track customer sales, manage transactions, and monitor revenue from product sales.</p>
        </div>
    </x-slot>

    <style>
        * { box-sizing: border-box; }

        .sales-shell {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f8f4 48%, #f8fafc 100%);
        }

        .sales-panel,
        .sales-table-card {
            border: 1px solid rgba(15, 23, 42, .08);
            background: #ffffff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
        }

        .sales-panel:hover,
        .sales-table-card:hover {
            box-shadow: 0 14px 34px rgba(15, 23, 42, .1);
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

        .sales-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .sales-table {
            border-collapse: separate !important;
            border-spacing: 0;
        }

        .sales-table thead th {
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

        .sales-table tbody td {
            border-bottom: 1px solid rgba(15, 23, 42, .06);
            color: #334155;
            font-size: .875rem;
            padding: 1rem !important;
            vertical-align: top;
        }

        .sales-table tbody tr {
            transition: background-color .16s ease;
        }

        .sales-table tbody tr:hover {
            background: #f8fafc;
        }

        .sales-table tbody tr:last-child td {
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

    <div class="sales-shell min-h-screen py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="sales-panel reveal rounded-2xl p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Sales transactions</h3>
                            <p class="text-sm font-medium text-slate-500">View all sales records, customer orders, and transaction history.</p>
                        </div>
                    </div>

                    <a href="{{ route('sale.create') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/15 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200 sm:w-auto">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                        </svg>
                        Add Sale
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

            <section class="sales-table-card reveal rounded-2xl p-4 sm:p-6">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">All sales transactions</h3>
                        <p class="mt-1 text-sm font-medium text-slate-500">Complete history of customer sales and revenue records.</p>
                    </div>
                    <span class="w-fit rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-emerald-700">
                        {{ number_format($sales->count()) }} records
                    </span>
                </div>

                <div class="sales-table-wrap">
                    <table id="sales-table" class="sales-table min-w-full">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Customer</th>
                                <th>Rate / Sales Unit</th>
                                <th>Amount</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sales as $sale)
                                <tr>
                                    <td class="font-semibold text-slate-600">{{ $sale->Date ? \Carbon\Carbon::parse($sale->Date)->format('d M Y') : 'Not set' }}</td>
                                    <td>
                                        <div class="flex items-start gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-sm font-black text-slate-700">
                                                {{ mb_strtoupper(mb_substr($sale->product->name ?? 'P', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-extrabold text-slate-900">{{ $sale->product->name ?? 'Unknown' }}</p>
                                                @if($sale->product?->salesUnit)
                                                    <p class="mt-1 text-xs font-medium text-slate-500">{{ $sale->quantity }} {{ $sale->product->salesUnit->name }}</p>
                                                @else
                                                    <p class="mt-1 text-xs font-medium text-slate-500">{{ $sale->quantity }} units</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($sale->product?->salesUnit)
                                            <span class="inline-flex items-center gap-1 font-extrabold text-emerald-700">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                                </svg>
                                                {{ $sale->quantity }} {{ $sale->product->salesUnit->name }}
                                            </span>
                                        @else
                                            <span class="font-extrabold text-slate-900">{{ $sale->quantity }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-start gap-2">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-xs font-black text-blue-700">
                                                {{ mb_strtoupper(mb_substr($sale->customer->name ?? 'C', 0, 1)) }}
                                            </div>
                                            <span class="font-semibold text-slate-700">{{ $sale->customer->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="space-y-1">
                                            <span class="font-black text-slate-900">Rs. {{ number_format($sale->rate, 2) }}</span>
                                            @if ($sale->product?->salesUnit)
                                                <p class="text-xs font-semibold text-slate-500">/ {{ $sale->product->salesUnit->name }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="inline-flex items-center gap-1 font-black text-emerald-700">
                                            <!-- <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg> -->
                                            Rs. {{ number_format($sale->amount, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex justify-end gap-2">
                                            <a class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 transition hover:bg-emerald-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-emerald-100" href="{{ route('sale.edit', $sale) }}" title="Edit sale" aria-label="Edit sale">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('sale.destroy', $sale) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-700 transition hover:bg-rose-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-rose-100" onclick="return confirm('Delete this Sale?')" title="Delete sale" aria-label="Delete sale">
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
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <p class="text-base font-extrabold text-slate-900">No sales transactions found</p>
                                            <p class="mt-1 text-sm font-medium text-slate-500">Add your first sale to begin tracking customer orders.</p>
                                            <a href="{{ route('sale.create') }}" class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/15 transition hover:bg-emerald-700">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                                                </svg>
                                                Add Sale
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
