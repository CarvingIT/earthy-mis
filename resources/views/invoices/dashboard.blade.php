<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Financial operations</p>
                <h2 class="text-3xl font-extrabold leading-tight text-slate-900">Invoices</h2>
            </div>
            <p class="max-w-xl text-sm font-medium text-slate-500">Generate, retry, and monitor monthly transport invoice dispatches and statuses.</p>
        </div>
    </x-slot>

    <style>
        * { box-sizing: border-box; }

        .invoices-shell {
            background:
                linear-gradient(180deg, #f8fafc 0%, #f1f8f4 48%, #f8fafc 100%);
        }

        .invoice-panel,
        .invoice-stat,
        .invoice-table-card {
            border: 1px solid rgba(15, 23, 42, .08);
            background: #ffffff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
        }

        .invoice-stat {
            position: relative;
            overflow: hidden;
            transition: box-shadow .18s ease, border-color .18s ease;
        }

        .invoice-stat::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--stat-tint), transparent 50%);
            opacity: .95;
            pointer-events: none;
        }

        .invoice-stat::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--stat-accent);
        }

        .invoice-stat:hover,
        .invoice-table-card:hover {
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

        .invoices-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .invoices-table {
            border-collapse: separate !important;
            border-spacing: 0;
        }

        .invoices-table thead th {
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

        .invoices-table tbody td {
            border-bottom: 1px solid rgba(15, 23, 42, .06);
            color: #334155;
            font-size: .875rem;
            padding: 1rem !important;
            vertical-align: middle;
        }

        .invoices-table tbody tr {
            transition: background-color .16s ease;
        }

        .invoices-table tbody tr:hover {
            background: #f8fafc;
        }

        .invoices-table tbody tr:last-child td {
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
        $stats = [
            [
                'label' => 'Total Societies',
                'value' => number_format($totalSocieties),
                'note' => 'Active billing targets',
                'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                'style' => '--stat-accent: linear-gradient(135deg, #0284c7, #22d3ee); --stat-tint: rgba(14, 165, 233, .15); --stat-shadow: rgba(14, 165, 233, .3); --stat-text: #0369a1;',
            ],
            [
                'label' => 'Successful Sent',
                'value' => number_format($sentCount),
                'note' => 'Invoices dispatched via mail',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'style' => '--stat-accent: linear-gradient(135deg, #059669, #84cc16); --stat-tint: rgba(16, 185, 129, .16); --stat-shadow: rgba(16, 185, 129, .3); --stat-text: #047857;',
            ],
            [
                'label' => 'Pending Queue',
                'value' => number_format($pendingCount),
                'note' => 'Awaiting generation/send',
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'style' => '--stat-accent: linear-gradient(135deg, #4f46e5, #06b6d4); --stat-tint: rgba(99, 102, 241, .14); --stat-shadow: rgba(79, 70, 229, .28); --stat-text: #4338ca;',
            ],
            [
                'label' => 'Failed Jobs',
                'value' => number_format($failedCount),
                'note' => 'Jobs with delivery errors',
                'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                'style' => '--stat-accent: linear-gradient(135deg, #ef4444, #f97316); --stat-tint: rgba(239, 68, 68, .15); --stat-shadow: rgba(239, 68, 68, .3); --stat-text: #b91c1c;',
            ],
        ];
    @endphp

    <div class="invoices-shell min-h-screen py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            
            <!-- Dashboard Operations Panel -->
            <section class="invoice-panel reveal rounded-2xl p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6M3 21h18M5 21V7l7-4 7 4v14"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Invoice directory</h3>
                            <p class="text-sm font-medium text-slate-500">Manage dispatch parameters, months, and batch operations.</p>
                        </div>
                    </div>

                    <!-- Compact Action Bar -->
                    <div class="flex flex-wrap items-center gap-2">
                        <form method="GET" action="{{ route('invoices.index') }}" class="inline-flex items-center">
                            <label for="month-select" class="sr-only">Select Month</label>
                            <input type="month" id="month-select" name="month" value="{{ $month }}" 
                                   onchange="this.form.submit()"
                                   class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs font-bold text-slate-700 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10">
                        </form>

                        <form method="POST" action="{{ route('invoices.global-dispatch') }}" class="inline-block">
                            @csrf
                            <input type="hidden" name="month" value="{{ $month }}">
                            <button type="submit" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg bg-slate-900 px-3.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Global Dispatch
                            </button>
                        </form>

                        @if ($failedCount > 0)
                            <form method="POST" action="{{ route('invoices.retry-failed') }}" class="inline-block">
                                @csrf
                                <input type="hidden" name="month" value="{{ $month }}">
                                <button type="submit" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-3.5 text-xs font-bold text-rose-700 transition hover:bg-rose-600 hover:text-white">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3 3L22 4"/>
                                    </svg>
                                    Retry Failed
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </section>

            <!-- Alert Messages -->
            @if(session('success'))
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

            @if(session('error'))
                <div class="reveal rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-bold text-rose-800 shadow-sm" data-dismissible-alert>
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button type="button" class="-mr-1 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-rose-700 transition hover:bg-rose-100 focus:outline-none focus:ring-4 focus:ring-rose-200" data-dismiss-alert aria-label="Dismiss alert">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Dynamic Stats Cards Row -->
            <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($stats as $index => $stat)
                    <article class="invoice-stat reveal rounded-2xl p-5" style="{{ $stat['style'] }} --reveal-delay: {{ $index * 70 }}ms;">
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

            <!-- Invoices Table Card -->
            <section class="invoice-table-card reveal rounded-2xl p-4 sm:p-6">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">Invoices List</h3>
                        <p class="mt-1 text-sm font-medium text-slate-500">Primary billing parameters, amounts, and live dispatch statuses.</p>
                    </div>
                    <span class="w-fit rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-emerald-700">
                        {{ Carbon\Carbon::parse($month . '-01')->format('F Y') }}
                    </span>
                </div>

                <div class="invoices-table-wrap">
                    <table class="invoices-table min-w-full">
                        <thead>
                            <tr>
                                <th>Society Name</th>
                                <th>Billing Month</th>
                                <th>Flats & Rate</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Processed At</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @include('invoices.partials.rows', ['allSocieties' => $allSocieties, 'month' => $month])
                        </tbody>
                    </table>
                </div>

                <!-- Load More & Informative Pagination Controls -->
                <div id="pagination-controls" class="mt-6 border-t border-slate-100 bg-slate-50/50 rounded-xl px-5 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p id="pagination-info" class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                        Showing {{ $allSocieties->count() }} of {{ $allSocieties->total() }} invoices ({{ $allSocieties->total() - $allSocieties->count() }} left)
                    </p>
                    
                    @if ($allSocieties->hasMorePages())
                        <button id="load-more-btn" type="button" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-900 hover:text-white">
                            <span>Load More</span>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    @endif
                </div>
            </section>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Animate Cards into view
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

                // Load More pagination logic
                let nextPageUrl = '{!! $allSocieties->nextPageUrl() ? $allSocieties->nextPageUrl() . "&month=" . $month . "&ajax=1" : "" !!}';
                let hasMore = {{ $allSocieties->hasMorePages() ? 'true' : 'false' }};
                let total = {{ $allSocieties->total() }};
                let loaded = {{ $allSocieties->count() }};

                const loadMoreBtn = document.getElementById('load-more-btn');
                const infoText = document.getElementById('pagination-info');
                const tableBody = document.querySelector('.invoices-table tbody');
                let isLoading = false;

                if (loadMoreBtn) {
                    loadMoreBtn.addEventListener('click', async () => {
                        if (isLoading || !hasMore || !nextPageUrl) return;
                        
                        isLoading = true;
                        loadMoreBtn.disabled = true;
                        loadMoreBtn.innerHTML = `
                            <svg class="animate-spin h-4 w-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Loading...</span>
                        `;

                        try {
                            const response = await fetch(nextPageUrl, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            const data = await response.json();
                            
                            // Append rows HTML
                            tableBody.insertAdjacentHTML('beforeend', data.html);
                            
                            // Update counts
                            loaded += data.count;
                            total = data.total;
                            hasMore = data.hasMore;
                            nextPageUrl = data.nextPageUrl;

                            const remaining = Math.max(0, total - loaded);
                            infoText.textContent = `Showing ${loaded} of ${total} invoices (${remaining} left)`;
                            
                            if (!hasMore) {
                                loadMoreBtn.style.display = 'none';
                            } else {
                                loadMoreBtn.disabled = false;
                                loadMoreBtn.innerHTML = `
                                    <span>Load More</span>
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                `;
                            }
                        } catch (error) {
                            console.error('Error loading invoices:', error);
                            loadMoreBtn.disabled = false;
                            loadMoreBtn.innerHTML = `
                                <span>Try Again</span>
                            `;
                        } finally {
                            isLoading = false;
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
