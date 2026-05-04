<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Business intelligence</p>
                <h2 class="text-3xl font-extrabold leading-tight text-slate-900">Analytics & Reports</h2>
            </div>
            <p class="max-w-xl text-sm font-medium text-slate-500">A focused view of money, stock, fleet activity, and operational momentum.</p>
        </div>
    </x-slot>

    <style>
        * { box-sizing: border-box; }

        .analytics-shell {
            background:
                linear-gradient(180deg, #f8fafc 0%, #f1f8f4 48%, #f8fafc 100%);
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
            min-height: 260px;
        }

        .doughnut-wrap {
            height: 310px;
        }

        .summary-card {
            border: 1px solid rgba(15, 23, 42, .08);
            background: #ffffff;
            box-shadow: 0 8px 18px rgba(15, 23, 42, .05);
        }

        @media (max-width: 640px) {
            .chart-canvas-wrap { min-height: 220px; }
            .doughnut-wrap { height: 270px; }
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
                'label' => 'Total Sales',
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
                'label' => 'Total Profit',
                'tag' => 'Profit',
                'value' => '₹' . $formatIndianNumber($stats['total_profit'] ?? 0),
                'note' => 'Net earnings',
                'icon' => 'M5 13l4 4L19 7',
                'style' => '--metric-accent: linear-gradient(135deg, #059669, #84cc16); --metric-tint: rgba(16, 185, 129, .16); --metric-shadow: rgba(16, 185, 129, .3); --metric-text: #047857;',
            ],
            [
                'key' => 'total_vehicles_km',
                'label' => 'Vehicle Distance',
                'tag' => 'Fleet',
                'value' => $formatIndianNumber($stats['total_vehicles_km'] ?? 0) . ' km',
                'note' => 'Fleet operations',
                'icon' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H3v11h2m8 0h2m4 0h2v-5l-3-4h-5',
                'style' => '--metric-accent: linear-gradient(135deg, #4f46e5, #06b6d4); --metric-tint: rgba(99, 102, 241, .14); --metric-shadow: rgba(79, 70, 229, .28); --metric-text: #4338ca;',
            ],
        ];

        $chartCards = [
            ['id' => 'profitLossChart', 'title' => 'Profit, Sales & Cost Analysis', 'badge' => 'Momentum', 'summary' => 'profitLossSummary', 'style' => '--chart-accent: linear-gradient(135deg, #0ea5e9, #10b981); --chart-shadow: rgba(14, 165, 233, .28); --chart-text: #0369a1; --chart-tint: rgba(14, 165, 233, .1); --chart-border: rgba(14, 165, 233, .18);'],
            ['id' => 'stockChart', 'title' => 'Stock Quantity Overview', 'badge' => 'Inventory', 'summary' => 'stockSummary', 'style' => '--chart-accent: linear-gradient(135deg, #06b6d4, #22c55e); --chart-shadow: rgba(6, 182, 212, .25); --chart-text: #0e7490; --chart-tint: rgba(6, 182, 212, .1); --chart-border: rgba(6, 182, 212, .18);'],
            ['id' => 'stockByProductChart', 'title' => 'Top 10 Products by Stock', 'badge' => 'Leaders', 'style' => '--chart-accent: linear-gradient(135deg, #f59e0b, #84cc16); --chart-shadow: rgba(245, 158, 11, .25); --chart-text: #a16207; --chart-tint: rgba(245, 158, 11, .12); --chart-border: rgba(245, 158, 11, .2);'],
            ['id' => 'saleChart', 'title' => 'Sales Revenue Trends', 'badge' => 'Revenue', 'summary' => 'saleSummary', 'style' => '--chart-accent: linear-gradient(135deg, #16a34a, #14b8a6); --chart-shadow: rgba(22, 163, 74, .25); --chart-text: #15803d; --chart-tint: rgba(34, 197, 94, .1); --chart-border: rgba(34, 197, 94, .18);'],
            ['id' => 'saleByProductChart', 'title' => 'Top 10 Products by Sales', 'badge' => 'Best sellers', 'style' => '--chart-accent: linear-gradient(135deg, #ec4899, #f97316); --chart-shadow: rgba(236, 72, 153, .22); --chart-text: #be185d; --chart-tint: rgba(236, 72, 153, .1); --chart-border: rgba(236, 72, 153, .18);'],
            ['id' => 'costChart', 'title' => 'Supply Cost Over Time', 'badge' => 'Spend', 'summary' => 'costSummary', 'style' => '--chart-accent: linear-gradient(135deg, #f97316, #ef4444); --chart-shadow: rgba(249, 115, 22, .25); --chart-text: #c2410c; --chart-tint: rgba(249, 115, 22, .1); --chart-border: rgba(249, 115, 22, .18);'],
            ['id' => 'costByConsumableChart', 'title' => 'Top 10 Consumables by Cost', 'badge' => 'Distribution', 'doughnut' => true, 'style' => '--chart-accent: linear-gradient(135deg, #7c3aed, #ec4899); --chart-shadow: rgba(124, 58, 237, .22); --chart-text: #6d28d9; --chart-tint: rgba(124, 58, 237, .1); --chart-border: rgba(124, 58, 237, .18);'],
            ['id' => 'vehicleChart', 'title' => 'Vehicle Running Distance', 'badge' => 'Fleet km', 'summary' => 'vehicleSummary', 'style' => '--chart-accent: linear-gradient(135deg, #4f46e5, #0ea5e9); --chart-shadow: rgba(79, 70, 229, .24); --chart-text: #4338ca; --chart-tint: rgba(79, 70, 229, .1); --chart-border: rgba(79, 70, 229, .18);'],
        ];
    @endphp

    <div class="analytics-shell min-h-screen py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
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
                            <option value="all" selected>All</option>
                            <option value="7">Last 7 Days</option>
                            <option value="14">Last 14 Days</option>
                            <option value="30">Last 30 Days</option>
                            <option value="60">Last 60 Days</option>
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

            <section class="grid grid-cols-1 gap-6">
                @foreach ($chartCards as $index => $chart)
                    <article class="chart-card reveal rounded-2xl p-5 sm:p-6" data-chart-id="{{ $chart['id'] }}" style="{{ $chart['style'] }} --reveal-delay: {{ min($index * 35, 105) }}ms;">
                        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="chart-mark h-11 w-2 shrink-0 rounded-full"></div>
                                <div class="min-w-0">
                                    <h3 class="truncate text-lg font-extrabold text-slate-900">{{ $chart['title'] }}</h3>
                                    <p class="text-sm font-medium text-slate-500">Updated for the selected period.</p>
                                </div>
                            </div>
                            <span class="chart-badge w-fit rounded-full px-3 py-1 text-xs font-extrabold uppercase tracking-wide">{{ $chart['badge'] }}</span>
                        </div>

                        <div class="{{ !empty($chart['doughnut']) ? 'doughnut-wrap' : 'chart-canvas-wrap' }}">
                            <canvas id="{{ $chart['id'] }}"></canvas>
                        </div>

                        @if (!empty($chart['summary']))
                            <div id="{{ $chart['summary'] }}" class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-4"></div>
                        @endif
                    </article>
                @endforeach
            </section>
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

        const palette = [
            colors.primary,
            colors.secondary,
            colors.warning,
            colors.danger,
            colors.tertiary,
            colors.quaternary,
            colors.rose,
            'rgb(20, 184, 166)',
            'rgb(99, 102, 241)',
            'rgb(132, 204, 22)',
        ];

        const lineColorByLabel = {
            sales: 'rgb(14, 165, 233)',
            cost: 'rgb(244, 63, 94)',
            costs: 'rgb(244, 63, 94)',
            profit: 'rgb(34, 197, 94)',
        };

        const chartOpts = {
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
            if (range.days === 'all') return 'All';
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

        function formatNumber(value, suffix = '') {
            return `${formatIndianNumber(value)}${suffix}`;
        }

        function loadMetricCards() {
            fetch(`/api/chart-stats${buildQuery()}`).then(r => r.json()).then(stats => {
                if (stats.error) return console.error(stats.error);

                const values = {
                    total_sales: formatMoney(stats.total_sales),
                    total_cost: formatMoney(stats.total_cost),
                    total_profit: formatMoney(stats.total_profit),
                    total_vehicles_km: formatNumber(stats.total_vehicles_km, ' km'),
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

            document.querySelectorAll('[data-chart-id].is-visible').forEach(loadVisibleChart);
        }

        function getChartConfig(id) {
            const q = buildQuery();
            const configs = {
                profitLossChart: { url: `/api/profit-loss-data${q}`, type: 'line', summaryFn: loadProfitSummary },
                stockChart: { url: `/api/stock-data${q}`, type: 'line', summaryFn: loadStockSummary },
                stockByProductChart: { url: `/api/stock-data-by-product${q}`, type: 'bar' },
                saleChart: { url: `/api/sale-data${q}`, type: 'line', summaryFn: loadSaleSummary },
                saleByProductChart: { url: `/api/sale-data-by-product${q}`, type: 'bar' },
                costChart: { url: `/api/cost-data${q}`, type: 'line', summaryFn: loadCostSummary },
                costByConsumableChart: { url: `/api/cost-data-by-consumable${q}`, type: 'doughnut' },
                vehicleChart: { url: `/api/vehicle-data${q}`, type: 'line', summaryFn: loadVehicleSummary },
            };

            return configs[id];
        }

        function loadVisibleChart(card) {
            const id = card.dataset.chartId;
            if (!id || charts[id]) return;

            const cfg = getChartConfig(id);
            if (!cfg) return;

            loadChart(id, cfg.url, cfg.type, cfg.summaryFn);
        }

        function initRevealAnimations() {
            const revealItems = document.querySelectorAll('.reveal');

            if (!('IntersectionObserver' in window)) {
                revealItems.forEach(item => {
                    item.classList.add('is-visible');
                    if (item.dataset.chartId) loadVisibleChart(item);
                });
                return;
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;

                    entry.target.classList.add('is-visible');
                    if (entry.target.dataset.chartId) loadVisibleChart(entry.target);
                    observer.unobserve(entry.target);
                });
            }, {
                rootMargin: '0px 0px -12% 0px',
                threshold: 0.16,
            });

            revealItems.forEach(item => observer.observe(item));
        }

        function loadChart(id, url, type, summaryFn) {
            fetch(url).then(r => r.json()).then(data => {
                if (data.error) return console.error(data.error);
                const el = document.getElementById(id);
                if (!el) return;

                const cfg = {
                    type,
                    data: {
                        labels: data.labels,
                        datasets: formatDatasets(data.datasets || [{ label: data.label, data: data.data }], type)
                    },
                    options: {
                        ...chartOpts,
                        ...(type === 'doughnut' ? {
                            cutout: '64%',
                            scales: {},
                            plugins: {
                                ...chartOpts.plugins,
                                legend: { ...chartOpts.plugins.legend, position: 'right', align: 'center' }
                            }
                        } : {})
                    }
                };

                charts[id] = new Chart(el.getContext('2d'), cfg);
                summaryFn?.(data);
            }).catch(e => console.error('Chart error:', e));
        }

        function formatDatasets(datasets, type) {
            return datasets.map((ds, i) => {
                const lineColor = getDatasetColor(ds, i);

                return {
                    ...ds,
                    borderWidth: type === 'bar' ? 0 : 3,
                    borderRadius: type === 'bar' ? 10 : 0,
                    borderSkipped: false,
                    fill: type === 'line',
                    tension: type === 'line' ? 0.38 : 0,
                    pointRadius: type === 'line' ? 3 : 0,
                    pointHoverRadius: type === 'line' ? 6 : 0,
                    pointBorderWidth: type === 'line' ? 3 : 0,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: lineColor,
                    backgroundColor: type === 'line'
                        ? toRgba(lineColor, 0.065)
                        : type === 'doughnut'
                            ? getBarColors(.84)
                            : getBarColors(.78),
                    borderColor: type === 'doughnut' ? '#ffffff' : lineColor,
                    hoverBackgroundColor: type === 'line' ? toRgba(lineColor, .12) : getBarColors(.92),
                };
            });
        }

        function getDatasetColor(dataset, index) {
            const normalizedLabel = String(dataset.label || '').toLowerCase();
            const match = Object.keys(lineColorByLabel).find(key => normalizedLabel.includes(key));

            return match ? lineColorByLabel[match] : getColorHex(index);
        }

        function getColorRgba(i, alpha) {
            const c = palette[i % palette.length].match(/\d+/g);
            return `rgba(${c[0]},${c[1]},${c[2]},${alpha})`;
        }

        function toRgba(color, alpha) {
            const c = color.match(/\d+/g);
            return `rgba(${c[0]},${c[1]},${c[2]},${alpha})`;
        }

        function getColorHex(i) {
            return palette[i % palette.length];
        }

        function getBarColors(alpha = .74) {
            return palette.map(color => {
                const c = color.match(/\d+/g);
                return `rgba(${c[0]},${c[1]},${c[2]},${alpha})`;
            });
        }

        function loadProfitSummary(d) {
            const s = d.summary;
            document.getElementById('profitLossSummary').innerHTML =
                createSumCard('Sales', `₹${s.total_sales.toLocaleString()}`, 'Total revenue in period', 'blue') +
                createSumCard('Cost', `₹${s.total_cost.toLocaleString()}`, 'Total operating spend', 'orange') +
                createSumCard('Profit', `₹${s.total_profit.toLocaleString()}`, 'Net gain after cost', 'green') +
                createSumCard('Margin', `${s.profit_margin}%`, 'Profit efficiency', 'purple');
        }

        function loadStockSummary(d) {
            if (!d.summary) return;
            const s = d.summary;
            document.getElementById('stockSummary').innerHTML =
                createSumCard('Total', s.total.toLocaleString(), 'Units available', 'cyan') +
                createSumCard('Average', s.average.toLocaleString(), 'Average per day', 'green') +
                createSumCard('Max', s.max.toLocaleString(), 'Highest stock day', 'yellow') +
                createSumCard('Period', getPeriodLabel(), 'Selected range', 'purple');
        }

        function loadSaleSummary(d) {
            if (!d.summary) return;
            const s = d.summary;
            document.getElementById('saleSummary').innerHTML =
                createSumCard('Total', `₹${s.total_amount.toLocaleString()}`, 'Revenue generated', 'green') +
                createSumCard('Daily', `₹${s.average_amount.toLocaleString()}`, 'Daily average', 'blue') +
                createSumCard('Peak', `₹${s.max_amount.toLocaleString()}`, 'Best day', 'yellow') +
                createSumCard('Trans', s.transaction_count.toLocaleString(), 'Transaction count', 'purple');
        }

        function loadCostSummary(d) {
            if (!d.summary) return;
            const s = d.summary;
            document.getElementById('costSummary').innerHTML =
                createSumCard('Total', `₹${s.total_cost.toLocaleString()}`, 'Total cost', 'orange') +
                createSumCard('Daily', `₹${s.average_cost.toLocaleString()}`, 'Average spend', 'red') +
                createSumCard('Peak', `₹${s.max_cost.toLocaleString()}`, 'Highest spend day', 'yellow') +
                createSumCard('Items', s.total_items.toLocaleString(), 'Quantity used', 'purple');
        }

        function loadVehicleSummary(d) {
            if (!d.summary) return;
            const s = d.summary;
            document.getElementById('vehicleSummary').innerHTML =
                createSumCard('Total', s.total_km.toLocaleString(), 'Fleet kilometers', 'blue') +
                createSumCard('Daily', s.average_km.toLocaleString(), 'Daily average', 'green') +
                createSumCard('Peak', s.max_km.toLocaleString(), 'Highest movement day', 'yellow') +
                createSumCard('Trips', s.trip_count.toLocaleString(), 'Completed trips', 'purple');
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
