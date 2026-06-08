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
                'key' => 'total',
                'label' => 'Total Societies',
                'value' => number_format($totalSocieties),
                'note' => 'Active billing targets',
                'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                'style' => '--stat-accent: linear-gradient(135deg, #0284c7, #22d3ee); --stat-tint: rgba(14, 165, 233, .15); --stat-shadow: rgba(14, 165, 233, .3); --stat-text: #0369a1;',
            ],
            [
                'key' => 'sent',
                'label' => 'Successful Sent',
                'value' => number_format($sentCount),
                'note' => 'Invoices dispatched via mail',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'style' => '--stat-accent: linear-gradient(135deg, #059669, #84cc16); --stat-tint: rgba(16, 185, 129, .16); --stat-shadow: rgba(16, 185, 129, .3); --stat-text: #047857;',
            ],
            [
                'key' => 'pending',
                'label' => 'Pending Queue',
                'value' => number_format($pendingCount),
                'note' => 'Awaiting generation/send',
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'style' => '--stat-accent: linear-gradient(135deg, #4f46e5, #06b6d4); --stat-tint: rgba(99, 102, 241, .14); --stat-shadow: rgba(79, 70, 229, .28); --stat-text: #4338ca;',
            ],
            [
                'key' => 'failed',
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

                        <button type="button" onclick="confirmGlobalDispatch()" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg bg-slate-900 px-3.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Global Dispatch
                        </button>

                        <button type="button" onclick="confirmGlobalGenerate()" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-emerald-600 bg-emerald-50 px-3.5 text-xs font-bold text-emerald-700 transition hover:bg-emerald-600 hover:text-white">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6M12 9v6m9-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Generate Invoices
                        </button>

                        @if ($failedCount > 0)
                            <button type="button" onclick="confirmRetryFailed()" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-3.5 text-xs font-bold text-rose-700 transition hover:bg-rose-600 hover:text-white">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3 3L22 4"/>
                                </svg>
                                Retry Failed
                            </button>
                        @endif

                        @if ($sentCount > 0)
                            <a href="{{ route('invoices.download-zip', ['month' => $month]) }}" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3.5 text-xs font-bold text-slate-700 transition hover:bg-slate-900 hover:text-white shadow-sm">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Download All (ZIP)
                            </a>
                        @endif

                        <button type="button" onclick="confirmClearQueue()" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3.5 text-xs font-bold text-slate-700 transition hover:bg-rose-600 hover:text-white hover:border-rose-600 shadow-sm">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Clear Month Invoices
                        </button>
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
                    <article onclick="showStatDetails('{{ $stat['key'] }}')" class="invoice-stat reveal rounded-2xl p-5 cursor-pointer hover:scale-[1.02] hover:shadow-xl active:scale-[0.98] transition-all duration-200" style="{{ $stat['style'] }} --reveal-delay: {{ $index * 70 }}ms;">
                        <div class="relative z-10">
                            <div class="mb-5 flex items-start justify-between gap-3">
                                <div class="stat-icon flex h-12 w-12 items-center justify-center rounded-xl text-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 border border-slate-100 rounded-full px-2 py-0.5 mt-1 hover:bg-slate-100">Click to View</span>
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
                                <th class="w-[30%] min-w-[240px]">Society Name</th>
                                <th class="w-[12%] min-w-[110px]">Billing Month</th>
                                <th class="w-[12%] min-w-[110px]">Flats & Rate</th>
                                <th class="w-[12%] min-w-[115px]">Total Amount</th>
                                <th class="w-[10%] min-w-[100px]">Status</th>
                                <th class="w-[14%] min-w-[140px]">Processed At</th>
                                <th class="text-right w-[10%] min-w-[110px]">Actions</th>
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

    <!-- Live Dispatch Progress Drawer -->
    <div id="dispatch-drawer" class="fixed inset-0 z-[60] flex items-end justify-center pointer-events-none">
        <div id="dispatch-drawer-backdrop" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>
        <div id="dispatch-drawer-panel" class="relative w-full max-w-2xl translate-y-full transition-transform duration-400 ease-out bg-white rounded-t-2xl shadow-2xl pointer-events-auto" style="max-height: 80vh;">
            <!-- Handle -->
            <div class="flex justify-center pt-3 pb-1">
                <div class="h-1 w-10 rounded-full bg-slate-200"></div>
            </div>
            <!-- Header -->
            <div class="px-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div id="drawer-icon" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                        <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Live Invoice Dispatch</h3>
                        <p id="drawer-subtitle" class="text-xs font-bold text-slate-400 mt-0.5">Initializing...</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Progress counters -->
                    <div class="flex items-center gap-2 text-[11px] font-bold">
                        <span class="flex items-center gap-1 text-emerald-600"><span class="h-2 w-2 rounded-full bg-emerald-500 inline-block"></span><span id="cnt-sent">0</span> sent</span>
                        <span class="flex items-center gap-1 text-amber-500"><span class="h-2 w-2 rounded-full bg-amber-400 inline-block"></span><span id="cnt-skipped">0</span> skipped</span>
                        <span class="flex items-center gap-1 text-rose-600"><span class="h-2 w-2 rounded-full bg-rose-500 inline-block"></span><span id="cnt-failed">0</span> failed</span>
                    </div>
                    <button id="drawer-close-btn" type="button" onclick="closeDispatchDrawer()" class="hidden inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <!-- Progress Bar -->
            <div class="px-6 py-3">
                <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                    <div id="dispatch-progress-bar" class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-emerald-600 transition-all duration-500" style="width: 0%"></div>
                </div>
                <p id="dispatch-progress-text" class="mt-1.5 text-[11px] font-bold text-slate-400">0 of 0 processed</p>
            </div>
            <!-- Live Feed -->
            <div id="dispatch-feed" class="overflow-y-auto px-6 pb-6 space-y-1" style="max-height: 42vh;">
                <!-- rows injected by JS -->
            </div>
        </div>
    </div>

    <!-- Beautiful Confirmation & Email Prompt Modal -->
    <div id="invoice-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
        <div class="relative w-full max-w-md scale-95 transform rounded-2xl bg-white p-6 shadow-2xl transition-all duration-200" id="invoice-modal-content">
            <!-- Close Button -->
            <button type="button" onclick="closeInvoiceModal()" class="absolute right-4 top-4 text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Modal Header -->
            <div class="flex items-center gap-3">
                <div id="modal-icon-container" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <div id="modal-icon">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 id="modal-title" class="text-base font-extrabold text-slate-900">Confirm Action</h3>
                    <p id="modal-subtitle" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Invoice Dispatch</p>
                </div>
            </div>

            <!-- Modal Body / Content -->
            <div class="mt-4">
                <div id="modal-description" class="text-sm font-semibold leading-relaxed text-slate-600"></div>
                
                <!-- Email Input (hidden by default) -->
                <div id="modal-email-group" class="mt-4" style="display: none;">
                    <label for="modal-email-input" class="text-xs font-bold text-slate-700 uppercase tracking-wide">Contact Person Email</label>
                    <input type="email" id="modal-email-input" class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 px-4 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/10" placeholder="e.g. chairman@society.com">
                    <p id="modal-email-error" class="mt-1.5 text-xs font-bold text-rose-600" style="display: none;">Please enter a valid email address.</p>
                </div>
            </div>

            <!-- Modal Footer / Actions -->
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" onclick="closeInvoiceModal()" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                    Cancel
                </button>
                <button type="button" id="modal-submit-btn" class="rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-700 transition shadow-md shadow-slate-900/10">
                    Confirm & Proceed
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Details Popup Modal -->
    <div id="stats-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200">
        <div class="relative w-full max-w-2xl scale-95 transform rounded-2xl bg-white p-6 shadow-2xl transition-all duration-200" id="stats-modal-content">
            <!-- Close Button -->
            <button type="button" onclick="closeStatsModal()" class="absolute right-4 top-4 text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Modal Header -->
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <div>
                    <h3 id="stats-modal-title" class="text-base font-extrabold text-slate-900">Statistics Details</h3>
                    <p id="stats-modal-subtitle" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Month: {{ Carbon\Carbon::parse($month . '-01')->format('F Y') }}</p>
                </div>
            </div>

            <!-- Modal Body (Detailed list) -->
            <div class="mt-4 max-h-[400px] overflow-y-auto pr-1" id="stats-modal-body">
                <!-- Loading State -->
                <div class="flex flex-col items-center justify-center py-12 gap-3" id="stats-loading">
                    <svg class="animate-spin h-8 w-8 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Loading details...</p>
                </div>

                <!-- Empty State -->
                <div class="hidden flex-col items-center justify-center py-12 gap-2 text-center" id="stats-empty">
                    <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0V9a2 2 0 00-2-2H6a2 2 0 00-2 2v2M9 11h6"/>
                    </svg>
                    <p class="text-sm font-bold text-slate-500">No records found matching this status.</p>
                </div>

                <!-- List Container -->
                <div class="divide-y divide-slate-100" id="stats-items-list" style="display: none;">
                    <!-- Will be dynamically populated -->
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="mt-6 border-t border-slate-100 pt-4 flex items-center justify-between">
                <div id="stats-modal-actions">
                    <!-- Dynamic action buttons e.g. Clear Pending Queue -->
                </div>
                <button type="button" onclick="closeStatsModal()" class="rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white hover:bg-emerald-700 transition shadow-md shadow-slate-900/10">
                    Close Details
                </button>
            </div>
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

            // Modal Logic
            const invoiceModal = document.getElementById('invoice-modal');
            const modalContent = document.getElementById('invoice-modal-content');
            const modalIconContainer = document.getElementById('modal-icon-container');
            const modalIcon = document.getElementById('modal-icon');
            const modalTitle = document.getElementById('modal-title');
            const modalDescription = document.getElementById('modal-description');
            const modalEmailGroup = document.getElementById('modal-email-group');
            const modalEmailInput = document.getElementById('modal-email-input');
            const modalEmailError = document.getElementById('modal-email-error');
            const modalSubmitBtn = document.getElementById('modal-submit-btn');

            let currentAction = null;

            window.openInvoiceModal = function() {
                invoiceModal.classList.remove('opacity-0', 'pointer-events-none');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            };

            window.closeInvoiceModal = function() {
                invoiceModal.classList.add('opacity-0', 'pointer-events-none');
                modalContent.classList.remove('scale-100');
                modalContent.classList.add('scale-95');
                modalEmailError.style.display = 'none';
            };

            window.confirmGlobalDispatch = function() {
                currentAction = { type: 'global' };
                modalIconContainer.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700';
                modalIcon.innerHTML = `
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                `;
                modalTitle.textContent = 'Confirm Global Dispatch';
                modalDescription.innerHTML = `
                    This will generate and email invoices to <strong>all active societies</strong> for <strong>{{ Carbon\Carbon::parse($month . '-01')->format('F Y') }}</strong>.<br><br>
                    <span class="text-xs font-bold text-amber-600 block bg-amber-50 rounded-lg p-2.5 border border-amber-200">
                        ⚡ Dispatch runs live — you'll see each society's result in real time. The page will not freeze.
                    </span>
                `;
                modalEmailGroup.style.display = 'none';
                modalSubmitBtn.textContent = 'Start Live Dispatch';
                modalSubmitBtn.className = 'rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-700 transition shadow-md shadow-slate-900/10';
                openInvoiceModal();
            };

            window.confirmRetryFailed = function() {
                currentAction = { type: 'retry-failed' };
                modalIconContainer.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-700';
                modalIcon.innerHTML = `
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3 3L22 4"/>
                    </svg>
                `;
                modalTitle.textContent = 'Retry Failed Invoices';
                modalDescription.innerHTML = `
                    This will recreate email delivery tasks for all <strong>failed</strong> invoices in <strong>{{ Carbon\Carbon::parse($month . '-01')->format('F Y') }}</strong>.
                `;
                modalEmailGroup.style.display = 'none';
                modalSubmitBtn.textContent = 'Retry Failed';
                modalSubmitBtn.className = 'rounded-xl bg-rose-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-rose-700 transition shadow-md shadow-rose-600/10';
                openInvoiceModal();
            };

            window.confirmSingleDispatch = function(societyId, societyName, email, actionType) {
                currentAction = { type: 'single', id: societyId, name: societyName, email: email, action: actionType };
                modalIconContainer.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700';
                modalIcon.innerHTML = `
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l8-5.333a2 2 0 012.22 0l8 5.333A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/>
                    </svg>
                `;
                
                let titleText = 'Generate & Send Invoice';
                let btnText = 'Send Invoice';
                if (actionType === 'resend') {
                    titleText = 'Regenerate & Re-send';
                    btnText = 'Regenerate & Send';
                } else if (actionType === 'retry') {
                    titleText = 'Retry Dispatch';
                    btnText = 'Retry';
                }

                modalTitle.textContent = titleText;
                
                if (email && email.trim() !== '') {
                    modalDescription.innerHTML = `
                        This will <strong>recalculate the billing amount</strong> based on current flats/rates, generate a fresh PDF, and queue email dispatch of the invoice for <strong>${societyName}</strong> to:<br>
                        <strong class="text-emerald-700 text-xs block mt-2 font-mono">${email}</strong>
                    `;
                    modalEmailGroup.style.display = 'none';
                    modalSubmitBtn.textContent = btnText;
                } else {
                    modalDescription.innerHTML = `
                        <strong>${societyName}</strong> does not have a registered contact email address.<br>
                        Please enter a valid email address below. It will be saved to the society profile and used to deliver the fresh invoice.
                    `;
                    modalEmailGroup.style.display = 'block';
                    modalEmailInput.value = '';
                    modalSubmitBtn.textContent = 'Save & Send';
                }
                modalSubmitBtn.className = 'rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-700 transition shadow-md shadow-slate-900/10';
                openInvoiceModal();
            };

            window.confirmClearQueue = function() {
                currentAction = { type: 'clear-queue' };
                modalIconContainer.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-700';
                modalIcon.innerHTML = `
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                `;
                modalTitle.textContent = 'Clear Month Invoices';
                modalDescription.innerHTML = `
                    This will <strong>permanently delete all invoice records and logs</strong> for the selected month (<strong>{{ Carbon\Carbon::parse($month . '-01')->format('F Y') }}</strong>).<br><br>
                    <span class="text-xs font-bold text-rose-600 block bg-rose-50 rounded-lg p-2.5 border border-rose-200">
                        ⚠️ Warning: This action cannot be undone. You will need to regenerate invoices to view or send them.
                    </span>
                `;
                modalEmailGroup.style.display = 'none';
                modalSubmitBtn.textContent = 'Clear All Invoices';
                modalSubmitBtn.className = 'rounded-xl bg-rose-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-rose-700 transition shadow-md shadow-rose-600/10';
                openInvoiceModal();
            };

            window.confirmClearPending = function() {
                currentAction = { type: 'clear-pending' };
                modalIconContainer.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-700';
                modalIcon.innerHTML = `
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                `;
                modalTitle.textContent = 'Clear Pending Queue';
                modalDescription.innerHTML = `
                    This will <strong>delete all pending/failed invoice records</strong> for the selected month (<strong>{{ Carbon\Carbon::parse($month . '-01')->format('F Y') }}</strong>).<br><br>
                    <span class="text-xs font-bold text-rose-600 block bg-rose-50 rounded-lg p-2.5 border border-rose-200">
                        ⚠️ Warning: This will reset all pending invoice records. It will not affect sent invoices.
                    </span>
                `;
                modalEmailGroup.style.display = 'none';
                modalSubmitBtn.textContent = 'Clear Pending Invoices';
                modalSubmitBtn.className = 'rounded-xl bg-rose-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-rose-700 transition shadow-md shadow-rose-600/10';
                openInvoiceModal();
            };

            window.confirmGlobalGenerate = function() {
                currentAction = { type: 'global-generate' };
                modalIconContainer.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700';
                modalIcon.innerHTML = `
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6M12 9v6m9-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                `;
                modalTitle.textContent = 'Generate Invoices Globally';
                modalDescription.innerHTML = `
                    This will calculate the billing amounts and generate invoice records for <strong>all societies</strong> for the selected month (<strong>{{ Carbon\Carbon::parse($month . '-01')->format('F Y') }}</strong>).<br><br>
                    <strong class="text-emerald-700 block bg-emerald-50 border border-emerald-100 p-2.5 rounded-lg text-xs leading-normal">
                        ℹ️ Invoices will be created in "Pending" status and will NOT be emailed to societies. You can review them here and dispatch them later.
                    </strong>
                `;
                modalEmailGroup.style.display = 'none';
                modalSubmitBtn.textContent = 'Generate Invoices';
                modalSubmitBtn.className = 'rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-700 transition shadow-md shadow-emerald-600/10';
                openInvoiceModal();
            };

            // Modal Submit Click Handler
            modalSubmitBtn.addEventListener('click', () => {
                if (!currentAction) return;

                if (currentAction.type === 'global') {
                    closeInvoiceModal();
                    startLiveDispatch('{{ $month }}');
                } else if (currentAction.type === 'retry-failed') {
                    // Submit retry failed form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('invoices.retry-failed') }}";
                    const token = document.querySelector('meta[name="csrf-token"]').content;
                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = token;
                    form.appendChild(tokenInput);
                    const monthInput = document.createElement('input');
                    monthInput.type = 'hidden';
                    monthInput.name = 'month';
                    monthInput.value = '{{ $month }}';
                    form.appendChild(monthInput);
                    document.body.appendChild(form);
                    form.submit();
                } else if (currentAction.type === 'clear-queue') {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('invoices.clear-queue') }}";
                    const token = document.querySelector('meta[name="csrf-token"]').content;
                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = token;
                    form.appendChild(tokenInput);
                    const monthInput = document.createElement('input');
                    monthInput.type = 'hidden';
                    monthInput.name = 'month';
                    monthInput.value = '{{ $month }}';
                    form.appendChild(monthInput);
                    document.body.appendChild(form);
                    form.submit();
                } else if (currentAction.type === 'clear-pending') {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('invoices.clear-pending') }}";
                    const token = document.querySelector('meta[name="csrf-token"]').content;
                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = token;
                    form.appendChild(tokenInput);
                    const monthInput = document.createElement('input');
                    monthInput.type = 'hidden';
                    monthInput.name = 'month';
                    monthInput.value = '{{ $month }}';
                    form.appendChild(monthInput);
                    document.body.appendChild(form);
                    form.submit();
                } else if (currentAction.type === 'global-generate') {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('invoices.generate-global') }}";
                    const token = document.querySelector('meta[name="csrf-token"]').content;
                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = token;
                    form.appendChild(tokenInput);
                    const monthInput = document.createElement('input');
                    monthInput.type = 'hidden';
                    monthInput.name = 'month';
                    monthInput.value = '{{ $month }}';
                    form.appendChild(monthInput);
                    document.body.appendChild(form);
                    form.submit();
                } else if (currentAction.type === 'single') {
                    let emailVal = '';
                    if (modalEmailGroup.style.display === 'block') {
                        emailVal = modalEmailInput.value.trim();
                        // Basic email validation regex
                        if (!emailVal || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
                            modalEmailError.style.display = 'block';
                            return;
                        }
                    }
                    
                    // Submit single dispatch form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/invoices/retry-single/${currentAction.id}`;
                    const token = document.querySelector('meta[name="csrf-token"]').content;
                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = token;
                    form.appendChild(tokenInput);
                    const monthInput = document.createElement('input');
                    monthInput.type = 'hidden';
                    monthInput.name = 'month';
                    monthInput.value = '{{ $month }}';
                    form.appendChild(monthInput);
                    
                    if (emailVal) {
                        const emailInput = document.createElement('input');
                        emailInput.type = 'hidden';
                        emailInput.name = 'email';
                        emailInput.value = emailVal;
                        form.appendChild(emailInput);
                    }
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });

            // Stats Details Modal Logic
            const statsModal = document.getElementById('stats-modal');
            const statsModalContent = document.getElementById('stats-modal-content');
            const statsModalTitle = document.getElementById('stats-modal-title');
            const statsLoading = document.getElementById('stats-loading');
            const statsEmpty = document.getElementById('stats-empty');
            const statsItemsList = document.getElementById('stats-items-list');

            const statsModalActions = document.getElementById('stats-modal-actions');

            window.openStatsModal = function() {
                statsModal.classList.remove('opacity-0', 'pointer-events-none');
                statsModalContent.classList.remove('scale-95');
                statsModalContent.classList.add('scale-100');
            };

            window.closeStatsModal = function() {
                statsModal.classList.add('opacity-0', 'pointer-events-none');
                statsModalContent.classList.remove('scale-100');
                statsModalContent.classList.add('scale-95');
            };

            window.showStatDetails = async function(status) {
                // Show loading state, hide list and empty states
                statsLoading.style.display = 'flex';
                statsEmpty.style.display = 'none';
                statsItemsList.style.display = 'none';
                statsItemsList.innerHTML = '';
                statsModalActions.innerHTML = '';
                openStatsModal();

                // Dynamically populate action button if status is pending
                if (status === 'pending') {
                    const clearPendingBtn = document.createElement('button');
                    clearPendingBtn.type = 'button';
                    clearPendingBtn.className = 'rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-xs font-bold text-rose-700 hover:bg-rose-600 hover:text-white transition';
                    clearPendingBtn.textContent = 'Clear Pending Queue';
                    clearPendingBtn.onclick = () => {
                        closeStatsModal();
                        confirmClearPending();
                    };
                    statsModalActions.appendChild(clearPendingBtn);
                }

                try {
                    const response = await fetch(`/invoices/stats-details?status=${status}&month={{ $month }}`);
                    const data = await response.json();
                    
                    statsModalTitle.textContent = data.title;
                    statsLoading.style.display = 'none';

                    if (!data.items || data.items.length === 0) {
                        statsEmpty.style.display = 'flex';
                    } else {
                        data.items.forEach(item => {
                            const row = document.createElement('div');
                            row.className = 'py-3.5 flex items-center justify-between gap-4';
                            row.innerHTML = `
                                <div>
                                    <h4 class="text-sm font-extrabold text-slate-800">${item.name}</h4>
                                    <p class="text-xs font-bold text-slate-400 mt-0.5">${item.info} &bull; <span class="font-mono text-[11px] text-slate-500">${item.detail}</span></p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-sm font-black text-slate-900">${item.amount}</span>
                                </div>
                            `;
                            statsItemsList.appendChild(row);
                        });
                        statsItemsList.style.display = 'block';
                    }
                } catch (error) {
                    console.error('Error fetching stat details:', error);
                    statsLoading.style.display = 'none';
                    statsEmpty.style.display = 'flex';
                }
            };
            // ─── Live Dispatch Engine ──────────────────────────────────────────
            const drawerEl       = document.getElementById('dispatch-drawer');
            const drawerBackdrop = document.getElementById('dispatch-drawer-backdrop');
            const drawerPanel    = document.getElementById('dispatch-drawer-panel');
            const drawerSubtitle = document.getElementById('drawer-subtitle');
            const drawerIcon     = document.getElementById('drawer-icon');
            const drawerCloseBtn = document.getElementById('drawer-close-btn');
            const dispatchFeed   = document.getElementById('dispatch-feed');
            const progressBar    = document.getElementById('dispatch-progress-bar');
            const progressText   = document.getElementById('dispatch-progress-text');
            const cntSent    = document.getElementById('cnt-sent');
            const cntSkipped = document.getElementById('cnt-skipped');
            const cntFailed  = document.getElementById('cnt-failed');

            function openDispatchDrawer() {
                drawerEl.classList.remove('pointer-events-none');
                drawerBackdrop.classList.remove('opacity-0');
                drawerBackdrop.classList.add('opacity-100');
                // Next tick for transition
                requestAnimationFrame(() => {
                    drawerPanel.classList.remove('translate-y-full');
                    drawerPanel.classList.add('translate-y-0');
                });
            }

            window.closeDispatchDrawer = function() {
                drawerPanel.classList.add('translate-y-full');
                drawerPanel.classList.remove('translate-y-0');
                drawerBackdrop.classList.remove('opacity-100');
                drawerBackdrop.classList.add('opacity-0');
                setTimeout(() => drawerEl.classList.add('pointer-events-none'), 350);
            };

            function addFeedRow(name, status, error) {
                const cfg = {
                    sent:    { dot: 'bg-emerald-500', badge: 'bg-emerald-50 text-emerald-700 border-emerald-200', label: 'Sent' },
                    skipped: { dot: 'bg-amber-400',   badge: 'bg-amber-50 text-amber-700 border-amber-200',   label: 'Skipped' },
                    failed:  { dot: 'bg-rose-500',    badge: 'bg-rose-50 text-rose-700 border-rose-200',     label: 'Failed' },
                }[status] || { dot: 'bg-slate-400', badge: 'bg-slate-50 text-slate-700 border-slate-200', label: status };

                const row = document.createElement('div');
                row.className = 'flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 bg-slate-50/70 border border-slate-100 opacity-0 translate-y-2 transition-all duration-300';
                row.innerHTML = `
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="h-2 w-2 rounded-full ${cfg.dot} shrink-0"></span>
                        <span class="text-sm font-semibold text-slate-800 truncate">${name}</span>
                        ${error ? `<span class="text-[10px] text-slate-400 truncate font-mono">${error.substring(0, 60)}</span>` : ''}
                    </div>
                    <span class="shrink-0 rounded-full border px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide ${cfg.badge}">${cfg.label}</span>
                `;
                dispatchFeed.appendChild(row);
                // Animate in
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        row.classList.remove('opacity-0', 'translate-y-2');
                    });
                });
                // Auto-scroll
                dispatchFeed.scrollTop = dispatchFeed.scrollHeight;
            }

            window.startLiveDispatch = async function(month) {
                // Reset state
                dispatchFeed.innerHTML = '';
                progressBar.style.width = '0%';
                progressText.textContent = 'Fetching societies...';
                drawerSubtitle.textContent = 'Fetching society list...';
                cntSent.textContent = cntSkipped.textContent = cntFailed.textContent = '0';
                drawerCloseBtn.classList.add('hidden');
                drawerIcon.innerHTML = `<svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
                openDispatchDrawer();

                const csrf = document.querySelector('meta[name="csrf-token"]').content;

                // 1. Fetch all societies
                let societies;
                try {
                    const res = await fetch('/invoices/societies-list', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    societies = await res.json();
                } catch (e) {
                    drawerSubtitle.textContent = 'Failed to fetch society list.';
                    drawerCloseBtn.classList.remove('hidden');
                    return;
                }

                if (!societies.length) {
                    drawerSubtitle.textContent = 'No societies found.';
                    drawerCloseBtn.classList.remove('hidden');
                    return;
                }

                const total = societies.length;
                let processed = 0, sent = 0, skipped = 0, failed = 0;

                // 2. Process each society one by one
                for (const society of societies) {
                    drawerSubtitle.textContent = `Sending to ${society.name}...`;
                    try {
                        const res = await fetch(`/invoices/dispatch-one/${society.id}`, {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ month }),
                        });
                        const data = await res.json();
                        processed++;
                        if (data.status === 'sent')         { sent++;    cntSent.textContent = sent; }
                        else if (data.status === 'skipped') { skipped++; cntSkipped.textContent = skipped; }
                        else                                { failed++;  cntFailed.textContent = failed; }
                        addFeedRow(data.name, data.status, data.error);
                    } catch (e) {
                        processed++;
                        failed++;
                        cntFailed.textContent = failed;
                        addFeedRow(society.name, 'failed', 'Network error');
                    }
                    progressBar.style.width = `${Math.round((processed / total) * 100)}%`;
                    progressText.textContent = `${processed} of ${total} processed`;
                }

                // 3. Done — update header
                drawerSubtitle.textContent = `Done! ${sent} sent, ${skipped} skipped, ${failed} failed.`;
                drawerIcon.innerHTML = `<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`;
                drawerIcon.className = 'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white';
                drawerCloseBtn.classList.remove('hidden');

                // 4. Soft-refresh the stats numbers without a full page reload
                setTimeout(() => window.location.reload(), 1800);
            };
            // ──────────────────────────────────────────────────────────────────
        </script>
    @endpush
</x-app-layout>
