<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Business intelligence</p>
                <h2 class="text-3xl font-extrabold leading-tight text-slate-900">Executive Dashboard</h2>
            </div>
            <p class="max-w-xl text-sm font-medium text-slate-500">Real-time insights into revenue, costs, profits, and fleet operations.</p>
        </div>
    </x-slot>

    <style>
        * { box-sizing: border-box; }

        .dashboard-shell {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f8f4 48%, #f8fafc 100%);
        }

        .insight-panel,
        .metric-card,
        .chart-card {
            border: 1px solid rgba(15, 23, 42, .08);
            background: #ffffff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
        }

        .metric-card {
            position: relative;
            overflow: hidden;
            transition: box-shadow .18s ease, border-color .18s ease;
        }

        .metric-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--metric-tint), transparent 48%);
            opacity: .95;
            pointer-events: none;
        }

        .metric-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--metric-accent);
        }

        .metric-card:hover,
        .chart-card:hover {
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

        .metric-icon {
            background: var(--metric-accent);
            box-shadow: 0 12px 28px var(--metric-shadow);
        }

        .metric-value {
            color: var(--metric-text);
            letter-spacing: 0;
        }

        .chart-card {
            contain: layout paint;
            transition: box-shadow .18s ease, border-color .18s ease;
        }

        .chart-mark {
            background: var(--chart-accent);
            box-shadow: 0 8px 18px var(--chart-shadow);
        }

        .chart-badge {
            color: var(--chart-text);
            background: var(--chart-tint);
            border: 1px solid var(--chart-border);
        }

        .chart-canvas-wrap {
            min-height: 280px;
        }

        .summary-card {
            border: 1px solid rgba(15, 23, 42, .08);
            background: #ffffff;
            box-shadow: 0 8px 18px rgba(15, 23, 42, .05);
        }

        @media (max-width: 640px) {
            .chart-canvas-wrap { min-height: 220px; }
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
        $formatIndianNumber = function ($value) {
            $value = (string) round((float) $value);
            $negative = str_starts_with($value, '-');
            $value = ltrim($value, '-');

            if (strlen($value) <= 3) {
                return ($negative ? '-' : '') . $value;
            }

            $lastThree = substr($value, -3);
            $remaining = substr($value, 0, -3);
            $remaining = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining);

            return ($negative ? '-' : '') . $remaining . ',' . $lastThree;
        };

        $metricCards = [
            [
                'key' => 'total_sales',
                'label' => 'Total Revenue',
                'tag' => 'Revenue',
                'value' => '₹' . $formatIndianNumber($stats['total_sales'] ?? 0),
                'note' => 'Period revenue',
                'icon' => 'M3 13.5l4.5-4.5 3 3L17 5.5M17 5.5h-5M17 5.5v5',
                'style' => '--metric-accent: linear-gradient(135deg, #0284c7, #22d3ee); --metric-tint: rgba(14, 165, 233, .15); --metric-shadow: rgba(14, 165, 233, .3); --metric-text: #0369a1;',
            ],
            [
                'key' => 'total_cost',
                'label' => 'Total Costs',
                'tag' => 'Costs',
                'value' => '₹' . $formatIndianNumber($stats['total_cost'] ?? 0),
                'note' => 'Operating expenses',
                'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-12v2m0 16v2m8-10h-2M6 12H4m14.364-6.364l-1.414 1.414M7.05 16.95l-1.414 1.414m12.728 0l-1.414-1.414M7.05 7.05L5.636 5.636',
                'style' => '--metric-accent: linear-gradient(135deg, #f59e0b, #f97316); --metric-tint: rgba(245, 158, 11, .16); --metric-shadow: rgba(245, 158, 11, .3); --metric-text: #b45309;',
            ],
            [
                'key' => 'total_profit',
                'label' => 'Net Profit',
                'tag' => 'Profit',
                'value' => '₹' . $formatIndianNumber($stats['total_profit'] ?? 0),
                'note' => 'Net earnings',
                'icon' => 'M5 13l4 4L19 7',
                'style' => '--metric-accent: linear-gradient(135deg, #059669, #84cc16); --metric-tint: rgba(16, 185, 129, .16); --metric-shadow: rgba(16, 185, 129, .3); --metric-text: #047857;',
            ],
            [
                'key' => 'profit_margin',
                'label' => 'Profit Margin',
                'tag' => 'Efficiency',
                'value' => ($stats['profit_margin'] ?? 0) . '%',
                'note' => 'Profit efficiency',
                'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                'style' => '--metric-accent: linear-gradient(135deg, #7c3aed, #ec4899); --metric-tint: rgba(124, 58, 237, .14); --metric-shadow: rgba(124, 58, 237, .28); --metric-text: #6d28d9;',
            ],
        ];
    @endphp

    <div class="dashboard-shell min-h-screen py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <!-- Date Range Filter -->
            <section class="insight-panel reveal rounded-2xl p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <label for="daysFilter" class="block text-sm font-extrabold text-slate-900">Analysis period</label>
                            <p class="text-sm font-medium text-slate-500">Short ranges reveal urgency; longer ranges reveal patterns.</p>
                        </div>
                    </div>

                    <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center lg:w-auto">
                        <select id="daysFilter" class="w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500 sm:w-52">
                            <option value="all" selected>All Time</option>
                            <option value="7">Last 7 Days</option>
                            <option value="30">Last 30 Days</option>
                            <option value="90">Last 90 Days</option>
                            <option value="custom">Custom Range</option>
                        </select>

                        <div id="dateRangeContainer" class="hidden w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                            <input type="date" id="startDate" class="rounded-xl border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <span class="hidden text-slate-400 sm:inline">to</span>
                            <input type="date" id="endDate" class="rounded-xl border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <button id="applyDateRange" class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/15 transition hover:bg-emerald-700">Apply</button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Financial Metrics -->
            <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($metricCards as $index => $card)
                    <article class="metric-card reveal rounded-2xl p-5" style="{{ $card['style'] }} --reveal-delay: {{ $index * 70 }}ms;">
                        <div class="relative z-10">
                            <div class="mb-5 flex items-start justify-between gap-3">
                                <div class="metric-icon flex h-12 w-12 items-center justify-center rounded-xl text-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                                    </svg>
                                </div>
                                <span class="rounded-full bg-white/80 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-slate-600">{{ $card['tag'] }}</span>
                            </div>
                            <p class="text-sm font-bold text-slate-500">{{ $card['label'] }}</p>
                            <p class="metric-value mt-2 text-3xl font-black" data-metric="{{ $card['key'] }}">{{ $card['value'] }}</p>
                            <p class="mt-3 text-xs font-semibold leading-5 text-slate-500" data-metric-note>{{ $card['note'] }}</p>
                        </div>
                    </article>
                @endforeach
            </section>

            <!-- Revenue/Cost/Profit Chart -->
            <section class="chart-card reveal rounded-2xl p-5 sm:p-6" style="--chart-accent: linear-gradient(135deg, #0ea5e9, #10b981); --chart-shadow: rgba(14, 165, 233, .28); --chart-text: #0369a1; --chart-tint: rgba(14, 165, 233, .1); --chart-border: rgba(14, 165, 233, .18); --reveal-delay: 100ms;">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="chart-mark h-11 w-2 shrink-0 rounded-full"></div>
                        <div class="min-w-0">
                            <h3 class="truncate text-lg font-extrabold text-slate-900">Revenue vs Costs vs Profit</h3>
                            <p class="text-sm font-medium text-slate-500">Track your financial performance - Revenue (blue), Costs (orange), Profit (green)</p>
                        </div>
                    </div>
                    <span class="chart-badge w-fit rounded-full px-3 py-1 text-xs font-extrabold uppercase tracking-wide">Financial Overview</span>
                </div>

                <div class="chart-canvas-wrap">
                    <canvas id="profitLossChart"></canvas>
                </div>

                <div id="profitLossSummary" class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-4"></div>
            </section>

            <!-- Vehicle Operations Section -->
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H3v11h2m8 0h2m4 0h2v-5l-3-4h-5"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900">Fleet Operations</h3>
                        <p class="text-sm font-medium text-slate-500">Vehicle utilization and performance metrics</p>
                    </div>
                </div>

                <!-- Vehicle Distance Comparison -->
                <section class="chart-card reveal rounded-2xl p-5 sm:p-6" style="--chart-accent: linear-gradient(135deg, #4f46e5, #0ea5e9); --chart-shadow: rgba(79, 70, 229, .24); --chart-text: #4338ca; --chart-tint: rgba(79, 70, 229, .1); --chart-border: rgba(79, 70, 229, .18); --reveal-delay: 150ms;">
                    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="chart-mark h-11 w-2 shrink-0 rounded-full"></div>
                            <div class="min-w-0">
                                <h3 class="truncate text-lg font-extrabold text-slate-900">Vehicle Distance Comparison</h3>
                                <p class="text-sm font-medium text-slate-500">Distance traveled by each vehicle</p>
                            </div>
                        </div>
                        <span class="chart-badge w-fit rounded-full px-3 py-1 text-xs font-extrabold uppercase tracking-wide">Fleet km</span>
                    </div>

                    <div class="chart-canvas-wrap">
                        <canvas id="vehicleDistanceChart"></canvas>
                    </div>
                </section>

                <!-- Vehicle Time Tracking Charts -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2" id="vehicleTimeChartsContainer">
                    <!-- Vehicle time charts will be dynamically loaded here -->
                </div>
            </div>

            <!-- Operations & Equipment Section -->
            <div class="space-y-6 pt-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900">Operations & Equipment</h3>
                        <p class="text-sm font-medium text-slate-500">Composting cycles, windrow status, JCB usage, and weight logistics</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Turning Duration Chart -->
                    <section class="chart-card reveal rounded-2xl p-5 sm:p-6" style="--chart-accent: linear-gradient(135deg, #10b981, #059669); --chart-shadow: rgba(16, 185, 129, .22); --chart-text: #047857; --chart-tint: rgba(16, 185, 129, .1); --chart-border: rgba(16, 185, 129, .18); --reveal-delay: 200ms;">
                        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="chart-mark h-11 w-2 shrink-0 rounded-full"></div>
                                <div class="min-w-0">
                                    <h3 class="truncate text-lg font-extrabold text-slate-900">Turning - Duration vs Date</h3>
                                    <p class="text-sm font-medium text-slate-500">Cumulative composting windrow turning duration (Hours)</p>
                                </div>
                            </div>
                            <span class="chart-badge w-fit rounded-full px-3 py-1 text-xs font-extrabold uppercase tracking-wide">Compost Turning</span>
                        </div>

                        <div class="chart-canvas-wrap">
                            <canvas id="turningChart"></canvas>
                        </div>
                    </section>

                    <!-- Windrow Cycles Chart -->
                    <section class="chart-card reveal rounded-2xl p-5 sm:p-6" style="--chart-accent: linear-gradient(135deg, #0ea5e9, #6366f1); --chart-shadow: rgba(14, 165, 233, .22); --chart-text: #0369a1; --chart-tint: rgba(14, 165, 233, .1); --chart-border: rgba(14, 165, 233, .18); --reveal-delay: 250ms;">
                        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="chart-mark h-11 w-2 shrink-0 rounded-full"></div>
                                <div class="min-w-0">
                                    <h3 class="truncate text-lg font-extrabold text-slate-900">Windrow Timeline</h3>
                                    <p class="text-sm font-medium text-slate-500">Loading & unloading duration cycles per windrow number</p>
                                </div>
                            </div>
                            <span class="chart-badge w-fit rounded-full px-3 py-1 text-xs font-extrabold uppercase tracking-wide">Windrow Cycles</span>
                        </div>

                        <div class="chart-canvas-wrap">
                            <canvas id="windrowChart"></canvas>
                        </div>
                    </section>

                    <!-- JCB Duration Chart -->
                    <section class="chart-card reveal rounded-2xl p-5 sm:p-6" style="--chart-accent: linear-gradient(135deg, #f59e0b, #d97706); --chart-shadow: rgba(245, 158, 11, .22); --chart-text: #b45309; --chart-tint: rgba(245, 158, 11, .1); --chart-border: rgba(245, 158, 11, .18); --reveal-delay: 300ms;">
                        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="chart-mark h-11 w-2 shrink-0 rounded-full"></div>
                                <div class="min-w-0">
                                    <h3 class="truncate text-lg font-extrabold text-slate-900">JCB - Duration vs Date</h3>
                                    <p class="text-sm font-medium text-slate-500">Cumulative machinery utilization duration (Hours)</p>
                                </div>
                            </div>
                            <span class="chart-badge w-fit rounded-full px-3 py-1 text-xs font-extrabold uppercase tracking-wide">JCB Operation</span>
                        </div>

                        <div class="chart-canvas-wrap">
                            <canvas id="jcbChart"></canvas>
                        </div>
                    </section>

                    <!-- Weight Chart -->
                    <section class="chart-card reveal rounded-2xl p-5 sm:p-6" style="--chart-accent: linear-gradient(135deg, #6366f1, #a855f7); --chart-shadow: rgba(99, 102, 241, .22); --chart-text: #4f46e5; --chart-tint: rgba(99, 102, 241, .1); --chart-border: rgba(99, 102, 241, .18); --reveal-delay: 350ms;">
                        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="chart-mark h-11 w-2 shrink-0 rounded-full"></div>
                                <div class="min-w-0">
                                    <h3 class="truncate text-lg font-extrabold text-slate-900">Weight - Net Weight vs Date</h3>
                                    <p class="text-sm font-medium text-slate-500">Total logistics net weight incoming to facility (Kgs)</p>
                                </div>
                            </div>
                            <span class="chart-badge w-fit rounded-full px-3 py-1 text-xs font-extrabold uppercase tracking-wide">Net Weight</span>
                        </div>

                        <div class="chart-canvas-wrap">
                            <canvas id="weightChart"></canvas>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Inventory & Consumables Section -->
            <div class="space-y-6 pt-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-100 text-teal-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900">Inventory & Consumables</h3>
                        <p class="text-sm font-medium text-slate-500">Stock quantities, values, and operational consumable costs</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Stock - Product List Qty & Value Chart -->
                    <section class="chart-card reveal rounded-2xl p-5 sm:p-6" style="--chart-accent: linear-gradient(135deg, #0ea5e9, #84cc16); --chart-shadow: rgba(14, 165, 233, .22); --chart-text: #0369a1; --chart-tint: rgba(14, 165, 233, .1); --chart-border: rgba(14, 165, 233, .18); --reveal-delay: 200ms;">
                        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="chart-mark h-11 w-2 shrink-0 rounded-full"></div>
                                <div class="min-w-0">
                                    <h3 class="truncate text-lg font-extrabold text-slate-900">Stock Quantity & Value</h3>
                                    <p class="text-sm font-medium text-slate-500">Real-time overview of current warehouse stock levels and cumulative market valuation (Base Units)</p>
                                </div>
                            </div>
                            <span class="chart-badge w-fit rounded-full px-3 py-1 text-xs font-extrabold uppercase tracking-wide">Stock Value</span>
                        </div>

                        <div class="chart-canvas-wrap">
                            <canvas id="stockProductQtyValueChart"></canvas>
                        </div>
                    </section>

                    <!-- Consumables - Costs vs Month Chart -->
                    <section class="chart-card reveal rounded-2xl p-5 sm:p-6" style="--chart-accent: linear-gradient(135deg, #f97316, #ef4444); --chart-shadow: rgba(249, 115, 22, .25); --chart-text: #c2410c; --chart-tint: rgba(249, 115, 22, .1); --chart-border: rgba(249, 115, 22, .18); --reveal-delay: 250ms;">
                        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="chart-mark h-11 w-2 shrink-0 rounded-full"></div>
                                <div class="min-w-0">
                                    <h3 class="truncate text-lg font-extrabold text-slate-900">Consumables Costs vs Month</h3>
                                    <p class="text-sm font-medium text-slate-500">Monthly breakdown of operating expenses on consumables</p>
                                </div>
                            </div>
                            <span class="chart-badge w-fit rounded-full px-3 py-1 text-xs font-extrabold uppercase tracking-wide">Spend Trend</span>
                        </div>

                        <div class="chart-canvas-wrap">
                            <canvas id="consumablesCostByMonthChart"></canvas>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const colors = {
            primary: 'rgb(14, 165, 233)',
            secondary: 'rgb(16, 185, 129)',
            tertiary: 'rgb(249, 115, 22)',
            quaternary: 'rgb(124, 58, 237)',
            success: 'rgb(34, 197, 94)',
            danger: 'rgb(239, 68, 68)',
            warning: 'rgb(245, 158, 11)',
            rose: 'rgb(236, 72, 153)',
        };

        let charts = {};

        function getDateRange() {
            const days = document.getElementById('daysFilter').value;
            if (days === 'custom') {
                const start = document.getElementById('startDate').value;
                const end = document.getElementById('endDate').value;
                if (start && end) return { start, end };
            }
            return { days };
        }

        function buildQuery() {
            const range = getDateRange();
            if (range.days) return `?days=${range.days}`;
            return `?start=${range.start}&end=${range.end}`;
        }

        function getPeriodLabel() {
            const range = getDateRange();
            if (range.start && range.end) return 'Custom';
            if (range.days === 'all') return 'All Time';
            return `${range.days} days`;
        }

        function formatIndianNumber(value) {
            const number = Math.round(Number(value || 0));
            const sign = number < 0 ? '-' : '';
            const digits = String(Math.abs(number));

            if (digits.length <= 3) return `${sign}${digits}`;

            const lastThree = digits.slice(-3);
            const remaining = digits.slice(0, -3).replace(/\B(?=(\d{2})+(?!\d))/g, ',');

            return `${sign}${remaining},${lastThree}`;
        }

        function formatMoney(value) {
            return `₹${formatIndianNumber(value)}`;
        }

        function loadMetricCards() {
            fetch(`/api/chart-stats${buildQuery()}`).then(r => r.json()).then(stats => {
                if (stats.error) return console.error(stats.error);

                const values = {
                    total_sales: formatMoney(stats.total_sales),
                    total_cost: formatMoney(stats.total_cost),
                    total_profit: formatMoney(stats.total_profit),
                    profit_margin: stats.profit_margin ? `${stats.profit_margin}%` : '0%',
                };

                Object.entries(values).forEach(([key, value]) => {
                    const el = document.querySelector(`[data-metric="${key}"]`);
                    if (el) el.textContent = value;
                });

                document.querySelectorAll('[data-metric-note]').forEach(note => {
                    note.textContent = `${getPeriodLabel()} data`;
                });
            }).catch(e => console.error('Stats error:', e));
        }

        function createSumCard(title, value, sub, color) {
            const colorMap = {
                blue: { accent: '#0ea5e9', tint: 'rgba(14, 165, 233, .12)', text: '#0369a1' },
                red: { accent: '#ef4444', tint: 'rgba(239, 68, 68, .11)', text: '#b91c1c' },
                green: { accent: '#10b981', tint: 'rgba(16, 185, 129, .12)', text: '#047857' },
                yellow: { accent: '#f59e0b', tint: 'rgba(245, 158, 11, .14)', text: '#a16207' },
                purple: { accent: '#7c3aed', tint: 'rgba(124, 58, 237, .1)', text: '#6d28d9' },
                cyan: { accent: '#06b6d4', tint: 'rgba(6, 182, 212, .11)', text: '#0e7490' },
                orange: { accent: '#f97316', tint: 'rgba(249, 115, 22, .12)', text: '#c2410c' },
                pink: { accent: '#ec4899', tint: 'rgba(236, 72, 153, .1)', text: '#be185d' },
            };
            const c = colorMap[color] || colorMap.blue;

            return `<div class="summary-card rounded-xl p-4" style="--summary-accent: ${c.accent}; --summary-tint: ${c.tint};">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <p class="text-xs font-extrabold uppercase tracking-wide text-slate-500">${title}</p>
                    <span class="h-2.5 w-2.5 rounded-full" style="background: ${c.accent}; box-shadow: 0 0 0 6px ${c.tint};"></span>
                </div>
                <p class="text-xl font-black" style="color: ${c.text};">${value}</p>
                <p class="mt-2 text-xs font-semibold text-slate-500">${sub}</p>
            </div>`;
        }

        function renderCharts() {
            Object.values(charts).forEach(c => c?.destroy?.());
            charts = {};
            loadMetricCards();

            // Load all visible charts
            loadProfitLossChart();
            loadVehicleDistanceChart();
            loadVehicleTimeCharts();
            loadStockProductQtyValueChart();
            loadConsumablesCostByMonthChart();
            loadTurningChart();
            loadWindrowChart();
            loadJcbChart();
            loadWeightChart();
        }

        function loadProfitLossChart() {
            fetch(`/api/profit-loss-data${buildQuery()}`).then(r => r.json()).then(data => {
                if (data.error) return console.error(data.error);
                const el = document.getElementById('profitLossChart');
                if (!el) return;

                charts.profitLossChart = new Chart(el.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: data.datasets.map((ds, i) => ({
                            ...ds,
                            borderWidth: 1.5,
                            borderRadius: 0,
                            fill: true,
                            tension: 0.38,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointBorderWidth: 3,
                            pointBackgroundColor: '#ffffff',
                        }))
                    },
                    options: {
                        ...getChartOptions(),
                        plugins: {
                            ...getChartOptions().plugins,
                            tooltip: {
                                ...getChartOptions().plugins.tooltip,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += '₹' + formatIndianNumber(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            ...getChartOptions().scales,
                            y: {
                                ...getChartOptions().scales.y,
                                title: {
                                    display: true,
                                    text: 'Amount (₹)',
                                    font: { size: 12, weight: '700' },
                                    color: '#64748b'
                                },
                                ticks: {
                                    ...getChartOptions().scales.y.ticks,
                                    callback: function(value) {
                                        return '₹' + formatIndianNumber(value);
                                    }
                                }
                            }
                        }
                    }
                });

                // Load summary
                if (data.summary) {
                    const s = data.summary;
                    document.getElementById('profitLossSummary').innerHTML =
                        createSumCard('Total Revenue', formatMoney(s.total_sales), 'Money earned from sales', 'blue') +
                        createSumCard('Total Costs', formatMoney(s.total_cost), 'Money spent on operations', 'orange') +
                        createSumCard('Net Profit', formatMoney(s.total_profit), s.total_profit >= 0 ? 'Revenue minus costs' : 'Loss incurred', s.total_profit >= 0 ? 'green' : 'red') +
                        createSumCard('Profit Margin', `${s.profit_margin}%`, 'Profit as % of revenue', 'purple');
                }
            }).catch(e => console.error('Chart error:', e));
        }

        function loadStockProductQtyValueChart() {
            fetch(`/api/stock-product-qty-value${buildQuery()}`).then(r => r.json()).then(data => {
                if (data.error) return console.error(data.error);
                const el = document.getElementById('stockProductQtyValueChart');
                if (!el) return;

                charts.stockProductQtyValueChart = new Chart(el.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Stock Quantity',
                                data: data.quantities,
                                backgroundColor: 'rgba(14, 165, 233, 0.7)',
                                borderColor: 'rgb(14, 165, 233)',
                                borderWidth: 1,
                                borderRadius: 6,
                                yAxisID: 'y-qty',
                            },
                            {
                                label: 'Stock Value (₹)',
                                data: data.values,
                                backgroundColor: 'rgba(132, 204, 22, 0.7)',
                                borderColor: 'rgb(132, 204, 22)',
                                borderWidth: 1,
                                borderRadius: 6,
                                yAxisID: 'y-val',
                            }
                        ]
                    },
                    options: {
                        ...getChartOptions(),
                        plugins: {
                            ...getChartOptions().plugins,
                            tooltip: {
                                ...getChartOptions().plugins.tooltip,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            if (context.dataset.yAxisID === 'y-val') {
                                                label += '₹' + formatIndianNumber(context.parsed.y);
                                            } else {
                                                label += formatIndianNumber(context.parsed.y);
                                            }
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: getChartOptions().scales.x,
                            'y-qty': {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Quantity',
                                    font: { size: 11, weight: '700' },
                                    color: '#0ea5e9'
                                },
                                grid: {
                                    color: 'rgba(14, 165, 233, 0.1)'
                                },
                                ticks: {
                                    font: { size: 10, weight: '600' },
                                    color: '#0ea5e9'
                                }
                            },
                            'y-val': {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Value (₹)',
                                    font: { size: 11, weight: '700' },
                                    color: '#84cc16'
                                },
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: { size: 10, weight: '600' },
                                    color: '#84cc16',
                                    callback: function(value) {
                                        return '₹' + formatIndianNumber(value);
                                    }
                                }
                            }
                        }
                    }
                });
            }).catch(e => console.error('Stock Qty/Val Chart error:', e));
        }

        function loadConsumablesCostByMonthChart() {
            fetch(`/api/consumables-cost-by-month${buildQuery()}`).then(r => r.json()).then(data => {
                if (data.error) return console.error(data.error);
                const el = document.getElementById('consumablesCostByMonthChart');
                if (!el) return;

                charts.consumablesCostByMonthChart = new Chart(el.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Consumables Cost',
                            data: data.costs,
                            borderColor: 'rgb(249, 115, 22)',
                            backgroundColor: 'rgba(249, 115, 22, 0.15)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.38,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointBorderWidth: 3,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: 'rgb(249, 115, 22)',
                        }]
                    },
                    options: {
                        ...getChartOptions(),
                        plugins: {
                            ...getChartOptions().plugins,
                            tooltip: {
                                ...getChartOptions().plugins.tooltip,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += '₹' + formatIndianNumber(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            ...getChartOptions().scales,
                            y: {
                                ...getChartOptions().scales.y,
                                title: {
                                    display: true,
                                    text: 'Cost (₹)',
                                    font: { size: 11, weight: '700' },
                                    color: '#f97316'
                                },
                                ticks: {
                                    ...getChartOptions().scales.y.ticks,
                                    callback: function(value) {
                                        return '₹' + formatIndianNumber(value);
                                    }
                                }
                            }
                        }
                    }
                });
            }).catch(e => console.error('Consumables cost by month chart error:', e));
        }

        function loadTurningChart() {
            fetch(`/api/turning-data${buildQuery()}`).then(r => r.json()).then(data => {
                if (data.error) return console.error(data.error);
                const el = document.getElementById('turningChart');
                if (!el) return;

                charts.turningChart = new Chart(el.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Duration (hours)',
                            data: data.data,
                            borderColor: 'rgb(16, 185, 129)',
                            backgroundColor: 'rgba(16, 185, 129, 0.12)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.38,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointBorderWidth: 3,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: 'rgb(16, 185, 129)',
                        }]
                    },
                    options: {
                        ...getChartOptions(),
                        scales: {
                            ...getChartOptions().scales,
                            y: {
                                ...getChartOptions().scales.y,
                                title: {
                                    display: true,
                                    text: 'Duration (hours)',
                                    font: { size: 11, weight: '700' },
                                    color: '#059669'
                                }
                            }
                        }
                    }
                });
            }).catch(e => console.error('Turning chart error:', e));
        }

        function loadWindrowChart() {
            fetch(`/api/windrow-data${buildQuery()}`).then(r => r.json()).then(data => {
                if (data.error) return console.error(data.error);
                const el = document.getElementById('windrowChart');
                if (!el) return;

                const chartData = data.data.map(item => ({
                    y: item.y,
                    x: [Date.parse(item.x[0]), Date.parse(item.x[1])],
                    startStr: item.start_date,
                    endStr: item.end_date,
                    isActive: item.is_active
                }));

                const minTime = Date.parse(data.min_date);
                const maxTime = Date.parse(data.max_date);

                charts.windrowChart = new Chart(el.getContext('2d'), {
                    type: 'bar',
                    data: {
                        datasets: [{
                            label: 'Loading Duration',
                            data: chartData,
                            backgroundColor: 'rgba(14, 165, 233, 0.8)',
                            borderColor: 'rgb(14, 165, 233)',
                            borderWidth: 1,
                            borderRadius: 4,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        ...getChartOptions(),
                        indexAxis: 'y',
                        interaction: { mode: 'nearest', intersect: true },
                        plugins: {
                            ...getChartOptions().plugins,
                            legend: { display: false },
                            tooltip: {
                                ...getChartOptions().plugins.tooltip,
                                callbacks: {
                                    title: (context) => context[0].raw.y,
                                    label: (context) => {
                                        const raw = context.raw;
                                        return `Duration: ${raw.startStr} to ${raw.endStr}${raw.isActive ? ' (Active)' : ''}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                type: 'linear',
                                min: minTime,
                                max: maxTime,
                                border: { display: false },
                                grid: { color: 'rgba(148, 163, 184, 0.12)', display: true },
                                ticks: {
                                    callback: function(value) {
                                        return new Date(value).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                                    },
                                    color: '#94A3B8',
                                    font: { size: 10, weight: '600' }
                                },
                                title: {
                                    display: true,
                                    text: 'Timeline',
                                    font: { size: 11, weight: '700' },
                                    color: '#64748b'
                                }
                            },
                            y: {
                                type: 'category',
                                labels: data.labels,
                                border: { display: false },
                                grid: { display: false },
                                ticks: {
                                    color: '#94A3B8',
                                    font: { size: 10, weight: '600' }
                                },
                                title: {
                                    display: true,
                                    text: 'Windrow Number',
                                    font: { size: 11, weight: '700' },
                                    color: '#64748b'
                                }
                            }
                        }
                    }
                });
            }).catch(e => console.error('Windrow chart error:', e));
        }

        function loadJcbChart() {
            fetch(`/api/jcb-data${buildQuery()}`).then(r => r.json()).then(data => {
                if (data.error) return console.error(data.error);
                const el = document.getElementById('jcbChart');
                if (!el) return;

                charts.jcbChart = new Chart(el.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Duration (hours)',
                            data: data.data,
                            borderColor: 'rgb(245, 158, 11)',
                            backgroundColor: 'rgba(245, 158, 11, 0.12)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.38,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointBorderWidth: 3,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: 'rgb(245, 158, 11)',
                        }]
                    },
                    options: {
                        ...getChartOptions(),
                        scales: {
                            ...getChartOptions().scales,
                            y: {
                                ...getChartOptions().scales.y,
                                title: {
                                    display: true,
                                    text: 'Duration (hours)',
                                    font: { size: 11, weight: '700' },
                                    color: '#d97706'
                                }
                            }
                        }
                    }
                });
            }).catch(e => console.error('JCB chart error:', e));
        }

        function loadWeightChart() {
            fetch(`/api/weight-data${buildQuery()}`).then(r => r.json()).then(data => {
                if (data.error) return console.error(data.error);
                const el = document.getElementById('weightChart');
                if (!el) return;

                charts.weightChart = new Chart(el.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Net Weight (Kgs)',
                            data: data.data,
                            borderColor: 'rgb(99, 102, 241)',
                            backgroundColor: 'rgba(99, 102, 241, 0.12)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.38,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointBorderWidth: 3,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: 'rgb(99, 102, 241)',
                        }]
                    },
                    options: {
                        ...getChartOptions(),
                        plugins: {
                            ...getChartOptions().plugins,
                            tooltip: {
                                ...getChartOptions().plugins.tooltip,
                                callbacks: {
                                    label: function(context) {
                                        return `Net Weight: ${formatIndianNumber(context.parsed.y)} Kgs`;
                                    }
                                }
                            }
                        },
                        scales: {
                            ...getChartOptions().scales,
                            y: {
                                ...getChartOptions().scales.y,
                                ticks: {
                                    ...getChartOptions().scales.y.ticks,
                                    callback: function(value) {
                                        return formatIndianNumber(value) + ' Kgs';
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Weight (Kgs)',
                                    font: { size: 11, weight: '700' },
                                    color: '#4f46e5'
                                }
                            }
                        }
                    }
                });
            }).catch(e => console.error('Weight chart error:', e));
        }

        function loadVehicleDistanceChart() {
            fetch(`/api/vehicle-distance-comparison${buildQuery()}`).then(r => r.json()).then(data => {
                if (data.error) return console.error(data.error);
                const el = document.getElementById('vehicleDistanceChart');
                if (!el) return;

                charts.vehicleDistanceChart = new Chart(el.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: data.datasets.map(ds => ({
                            ...ds,
                            borderWidth: 1.5,
                            fill: true,
                            tension: 0.38,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            pointBorderWidth: 3,
                            pointBackgroundColor: '#ffffff',
                        }))
                    },
                    options: {
                        ...getChartOptions(),
                        scales: {
                            ...getChartOptions().scales,
                            y: {
                                ...getChartOptions().scales.y,
                                title: {
                                    display: true,
                                    text: 'Distance (km)',
                                    font: { size: 11, weight: '700' },
                                    color: '#64748b'
                                }
                            }
                        }
                    }
                });
            }).catch(e => console.error('Chart error:', e));
        }

        function loadVehicleTimeCharts() {
            fetch(`/api/vehicle-time-data${buildQuery()}`).then(r => r.json()).then(data => {
                if (data.error) return console.error(data.error);
                
                const datasets = data.datasets;
                const container = document.getElementById('vehicleTimeChartsContainer');
                
                // Clear existing charts
                container.innerHTML = '';
                
                // Create a chart card for each vehicle
                const colorSchemes = [
                    { accent: 'linear-gradient(135deg, #06b6d4, #22c55e)', shadow: 'rgba(6, 182, 212, .25)', text: '#0e7490', tint: 'rgba(6, 182, 212, .1)', border: 'rgba(6, 182, 212, .18)' },
                    { accent: 'linear-gradient(135deg, #f59e0b, #ef4444)', shadow: 'rgba(245, 158, 11, .25)', text: '#c2410c', tint: 'rgba(245, 158, 11, .1)', border: 'rgba(245, 158, 11, .18)' },
                    { accent: 'linear-gradient(135deg, #7c3aed, #ec4899)', shadow: 'rgba(124, 58, 237, .25)', text: '#6d28d9', tint: 'rgba(124, 58, 237, .1)', border: 'rgba(124, 58, 237, .18)' },
                    { accent: 'linear-gradient(135deg, #14b8a6, #0ea5e9)', shadow: 'rgba(20, 184, 166, .25)', text: '#0f766e', tint: 'rgba(20, 184, 166, .1)', border: 'rgba(20, 184, 166, .18)' },
                    { accent: 'linear-gradient(135deg, #f97316, #f43f5e)', shadow: 'rgba(249, 115, 22, .25)', text: '#c2410c', tint: 'rgba(249, 115, 22, .1)', border: 'rgba(249, 115, 22, .18)' },
                    { accent: 'linear-gradient(135deg, #8b5cf6, #06b6d4)', shadow: 'rgba(139, 92, 246, .25)', text: '#7c3aed', tint: 'rgba(139, 92, 246, .1)', border: 'rgba(139, 92, 246, .18)' },
                ];
                
                datasets.forEach((vehicleData, index) => {
                    const canvasId = `vehicleTimeChart_${vehicleData.vehicle_id}`;
                    const colorScheme = colorSchemes[index % colorSchemes.length];
                    const delay = 200 + (index * 50);
                    
                    // Create chart card HTML
                    const cardHTML = `
                        <section class="chart-card reveal rounded-2xl p-5 sm:p-6 is-visible" style="--chart-accent: ${colorScheme.accent}; --chart-shadow: ${colorScheme.shadow}; --chart-text: ${colorScheme.text}; --chart-tint: ${colorScheme.tint}; --chart-border: ${colorScheme.border}; --reveal-delay: ${delay}ms;">
                            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex min-w-0 items-center gap-3">
                                    <div class="chart-mark h-11 w-2 shrink-0 rounded-full"></div>
                                    <div class="min-w-0">
                                        <h3 class="truncate text-lg font-extrabold text-slate-900">${vehicleData.label} - Operating Duration</h3>
                                        <p class="text-sm font-medium text-slate-500">Green: Work hours | Blue ▲: Start | Orange ◆: End</p>
                                    </div>
                                </div>
                                <span class="chart-badge w-fit rounded-full px-3 py-1 text-xs font-extrabold uppercase tracking-wide">Schedule</span>
                            </div>

                            <div class="chart-canvas-wrap">
                                <canvas id="${canvasId}"></canvas>
                            </div>
                        </section>
                    `;
                    
                    container.insertAdjacentHTML('beforeend', cardHTML);
                    
                    // Create the chart
                    setTimeout(() => {
                        loadVehicleTimeChart(canvasId, vehicleData);
                    }, 100);
                });
                
                // Show message if no vehicles with time data
                if (datasets.length === 0) {
                    container.innerHTML = `
                        <div class="col-span-full text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H3v11h2m8 0h2m4 0h2v-5l-3-4h-5"/>
                            </svg>
                            <p class="mt-4 text-lg font-semibold text-gray-500">No vehicle time data available</p>
                            <p class="mt-2 text-sm text-gray-400">Add logistics logs with start and end times to see operating hours here</p>
                        </div>
                    `;
                }
            }).catch(e => console.error('Chart error:', e));
        }

        function loadVehicleTimeChart(canvasId, vehicleData) {
            const el = document.getElementById(canvasId);
            if (!el) return;

            // Convert time strings to hours for plotting
            const startHours = vehicleData.start_times.map(t => timeToHours(t));
            const endHours = vehicleData.end_times.map(t => timeToHours(t));
            
            // Calculate duration in hours
            const durations = startHours.map((start, i) => {
                const end = endHours[i];
                return end > start ? (end - start) : 0;
            });

            charts[canvasId] = new Chart(el.getContext('2d'), {
                type: 'line',
                data: {
                    labels: vehicleData.labels,
                    datasets: [
                        {
                            label: 'Operating Duration',
                            data: durations,
                            borderColor: 'rgb(16, 185, 129)',
                            backgroundColor: 'rgba(16, 185, 129, 0.15)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.38,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            pointBorderWidth: 2,
                            pointBackgroundColor: '#ffffff',
                            yAxisID: 'y-duration',
                        },
                        {
                            label: 'Start Time',
                            data: startHours,
                            borderColor: 'rgb(14, 165, 233)',
                            backgroundColor: 'rgba(14, 165, 233, 0.1)',
                            borderWidth: 1,
                            borderDash: [5, 5],
                            fill: false,
                            tension: 0.38,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointStyle: 'triangle',
                            yAxisID: 'y-time',
                        },
                        {
                            label: 'End Time',
                            data: endHours,
                            borderColor: 'rgb(245, 158, 11)',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            borderWidth: 1,
                            borderDash: [5, 5],
                            fill: false,
                            tension: 0.38,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointStyle: 'rectRot',
                            yAxisID: 'y-time',
                        }
                    ]
                },
                options: {
                    ...getChartOptions(),
                    plugins: {
                        ...getChartOptions().plugins,
                        tooltip: {
                            ...getChartOptions().plugins.tooltip,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        if (context.dataset.yAxisID === 'y-duration') {
                                            // Show duration in hours and minutes
                                            const hours = Math.floor(context.parsed.y);
                                            const minutes = Math.round((context.parsed.y - hours) * 60);
                                            label += `${hours}h ${minutes}m`;
                                        } else {
                                            // Show time of day
                                            label += hoursToTime(context.parsed.y);
                                        }
                                    }
                                    return label;
                                }
                            }
                        },
                        legend: {
                            ...getChartOptions().plugins.legend,
                            labels: {
                                ...getChartOptions().plugins.legend.labels,
                                generateLabels: function(chart) {
                                    const datasets = chart.data.datasets;
                                    return datasets.map((dataset, i) => ({
                                        text: dataset.label,
                                        fillStyle: dataset.borderColor,
                                        strokeStyle: dataset.borderColor,
                                        lineWidth: dataset.borderWidth,
                                        hidden: !chart.isDatasetVisible(i),
                                        index: i,
                                        pointStyle: dataset.pointStyle || 'circle',
                                    }));
                                }
                            }
                        }
                    },
                    scales: {
                        x: getChartOptions().scales.x,
                        'y-duration': {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Duration (hours)',
                                font: { size: 12, weight: '700' },
                                color: '#10b981'
                            },
                            grid: {
                                color: 'rgba(16, 185, 129, 0.1)'
                            },
                            ticks: {
                                font: { size: 10, weight: '600' },
                                color: '#10b981',
                                callback: function(value) {
                                    const hours = Math.floor(value);
                                    const minutes = Math.round((value - hours) * 60);
                                    return `${hours}h${minutes > 0 ? minutes + 'm' : ''}`;
                                }
                            }
                        },
                        'y-time': {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            min: 0,
                            max: 24,
                            title: {
                                display: true,
                                text: 'Time of Day',
                                font: { size: 12, weight: '700' },
                                color: '#64748b'
                            },
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: { size: 10, weight: '600' },
                                color: '#64748b',
                                stepSize: 4,
                                callback: function(value) {
                                    return hoursToTime(value);
                                }
                            }
                        }
                    }
                }
            });
        }

        function timeToHours(timeStr) {
            if (!timeStr) return 0;
            const [hours, minutes] = timeStr.split(':').map(Number);
            return hours + (minutes / 60);
        }

        function hoursToTime(hours) {
            const h = Math.floor(hours);
            const m = Math.round((hours - h) * 60);
            return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`;
        }

        function getChartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            boxHeight: 8,
                            padding: 18,
                            font: { size: 11, weight: '700' },
                            color: '#475569',
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, .92)',
                        titleFont: { size: 12, weight: '800' },
                        bodyFont: { size: 12, weight: '600' },
                        padding: 12,
                        cornerRadius: 10,
                        displayColors: true,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: 'rgba(148, 163, 184, .18)' },
                        ticks: { font: { size: 10, weight: '600' }, color: '#94A3B8' }
                    },
                    x: {
                        border: { display: false },
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: '600' }, color: '#94A3B8', maxRotation: 0 }
                    }
                }
            };
        }

        function initRevealAnimations() {
            const revealItems = document.querySelectorAll('.reveal');

            if (!('IntersectionObserver' in window)) {
                revealItems.forEach(item => item.classList.add('is-visible'));
                return;
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                });
            }, {
                rootMargin: '0px 0px -12% 0px',
                threshold: 0.16,
            });

            revealItems.forEach(item => observer.observe(item));
        }

        document.getElementById('daysFilter').addEventListener('change', function() {
            document.getElementById('dateRangeContainer').style.display = this.value === 'custom' ? 'flex' : 'none';
            if (this.value !== 'custom') renderCharts();
        });

        document.getElementById('applyDateRange').addEventListener('click', renderCharts);

        document.addEventListener('DOMContentLoaded', function() {
            initRevealAnimations();
            renderCharts();
        });
    </script>
</x-app-layout>
