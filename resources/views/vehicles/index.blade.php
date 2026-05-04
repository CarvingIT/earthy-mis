<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Fleet operations</p>
                <h2 class="text-3xl font-extrabold leading-tight text-slate-900">Vehicles</h2>
            </div>
            <p class="max-w-xl text-sm font-medium text-slate-500">Manage vehicle registrations, fleet identity, and purchase records from one organized workspace.</p>
        </div>
    </x-slot>

    <style>
        * { box-sizing: border-box; }

        .vehicles-shell {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f8f4 48%, #f8fafc 100%);
        }

        .vehicle-panel,
        .vehicle-stat,
        .vehicle-table-card {
            border: 1px solid rgba(15, 23, 42, .08);
            background: #ffffff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
        }

        .vehicle-stat {
            position: relative;
            overflow: hidden;
            transition: box-shadow .18s ease, border-color .18s ease;
        }

        .vehicle-stat::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--stat-tint), transparent 50%);
            opacity: .95;
            pointer-events: none;
        }

        .vehicle-stat::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--stat-accent);
        }

        .vehicle-stat:hover,
        .vehicle-table-card:hover {
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

        .vehicles-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .vehicles-table {
            border-collapse: separate !important;
            border-spacing: 0;
        }

        .vehicles-table thead th {
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

        .vehicles-table tbody td {
            border-bottom: 1px solid rgba(15, 23, 42, .06);
            color: #334155;
            font-size: .875rem;
            padding: 1rem !important;
            vertical-align: middle;
        }

        .vehicles-table tbody tr {
            transition: background-color .16s ease;
        }

        .vehicles-table tbody tr:hover {
            background: #f8fafc;
        }

        .vehicles-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .vehicle-table-card .dt-container {
            color: #475569;
            font-size: .875rem;
        }

        .vehicle-table-card .dt-layout-row {
            align-items: center;
            gap: 1rem;
            margin: 0 0 1rem;
        }

        .vehicle-table-card .dt-layout-row:last-child {
            margin: 1rem 0 0;
        }

        .vehicle-table-card .dt-search label,
        .vehicle-table-card .dt-info {
            color: #64748b;
            font-size: .82rem;
            font-weight: 700;
        }

        .vehicle-table-card .dt-input {
            border: 1px solid #e2e8f0 !important;
            border-radius: .75rem !important;
            color: #334155;
            font-weight: 600;
            min-height: 2.65rem;
            outline: none;
            padding: .55rem .85rem !important;
            transition: border-color .16s ease, box-shadow .16s ease;
        }

        .vehicle-table-card .dt-input:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, .14);
        }

        .vehicle-table-card .dt-paging .dt-paging-button {
            border: 1px solid #e2e8f0 !important;
            border-radius: .65rem !important;
            color: #475569 !important;
            font-weight: 800;
            margin-left: .25rem;
            padding: .45rem .75rem !important;
        }

        .vehicle-table-card .dt-paging .dt-paging-button.current,
        .vehicle-table-card .dt-paging .dt-paging-button:hover {
            background: #0f172a !important;
            color: #ffffff !important;
        }

        @media (max-width: 640px) {
            .vehicle-table-card .dt-layout-row {
                align-items: stretch;
                flex-direction: column;
            }

            .vehicle-table-card .dt-search,
            .vehicle-table-card .dt-search input {
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
        $totalVehicles = $vehicles->count();
        $vehicleTypes = $vehicles->pluck('type')->filter()->map(fn ($type) => trim($type))->filter()->unique(fn ($type) => mb_strtolower($type))->count();
        $knownBrands = $vehicles->pluck('brand')->filter()->map(fn ($brand) => trim($brand))->filter()->unique(fn ($brand) => mb_strtolower($brand))->count();
        $purchaseCoverage = $totalVehicles > 0
            ? round(($vehicles->filter(fn ($vehicle) => filled($vehicle->purchased_on))->count() / $totalVehicles) * 100)
            : 0;

        $stats = [
            [
                'label' => 'Total Vehicles',
                'value' => number_format($totalVehicles),
                'note' => 'Registered fleet assets',
                'icon' => 'M5 17h14l-1.4-5.6A2 2 0 0015.66 10H8.34a2 2 0 00-1.94 1.4L5 17zM7 17a2 2 0 104 0M13 17a2 2 0 104 0M6 10l1-4h10l1 4',
                'style' => '--stat-accent: linear-gradient(135deg, #0284c7, #22d3ee); --stat-tint: rgba(14, 165, 233, .15); --stat-shadow: rgba(14, 165, 233, .3); --stat-text: #0369a1;',
            ],
            [
                'label' => 'Vehicle Types',
                'value' => number_format($vehicleTypes),
                'note' => 'Unique categories in use',
                'icon' => 'M4 7h16M4 12h16M4 17h16',
                'style' => '--stat-accent: linear-gradient(135deg, #059669, #84cc16); --stat-tint: rgba(16, 185, 129, .16); --stat-shadow: rgba(16, 185, 129, .3); --stat-text: #047857;',
            ],
            [
                'label' => 'Brands',
                'value' => number_format($knownBrands),
                'note' => 'Known manufacturers',
                'icon' => 'M12 3l7 4v10l-7 4-7-4V7l7-4zM12 12l7-4M12 12v9M12 12L5 8',
                'style' => '--stat-accent: linear-gradient(135deg, #4f46e5, #06b6d4); --stat-tint: rgba(99, 102, 241, .14); --stat-shadow: rgba(79, 70, 229, .28); --stat-text: #4338ca;',
            ],
            [
                'label' => 'Purchase Records',
                'value' => $purchaseCoverage . '%',
                'note' => 'With purchase date saved',
                'icon' => 'M8 7V3m8 4V3M5 11h14M6 5h12a1 1 0 011 1v13a1 1 0 01-1 1H6a1 1 0 01-1-1V6a1 1 0 011-1z',
                'style' => '--stat-accent: linear-gradient(135deg, #f59e0b, #f97316); --stat-tint: rgba(245, 158, 11, .16); --stat-shadow: rgba(245, 158, 11, .3); --stat-text: #b45309;',
            ],
        ];
    @endphp

    <div class="vehicles-shell min-h-screen py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="vehicle-panel reveal rounded-2xl p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17h14l-1.4-5.6A2 2 0 0015.66 10H8.34a2 2 0 00-1.94 1.4L5 17zM7 17a2 2 0 104 0M13 17a2 2 0 104 0M6 10l1-4h10l1 4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Fleet directory</h3>
                            <p class="text-sm font-medium text-slate-500">Use search and sorting to find vehicles, registrations, and asset details quickly.</p>
                        </div>
                    </div>

                    <a href="{{ route('vehicles.create') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/15 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200 sm:w-auto">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                        </svg>
                        Add Vehicle
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
                    <article class="vehicle-stat reveal rounded-2xl p-5" style="{{ $stat['style'] }} --reveal-delay: {{ $index * 70 }}ms;">
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

            <section class="vehicle-table-card reveal rounded-2xl p-4 sm:p-6">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">All vehicles</h3>
                        <p class="mt-1 text-sm font-medium text-slate-500">Registration, vehicle identity, and purchase information.</p>
                    </div>
                    <span class="w-fit rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-emerald-700">
                        {{ number_format($totalVehicles) }} records
                    </span>
                </div>

                <div class="vehicles-table-wrap">
                    <table id="vehicles-table" data-datatable class="vehicles-table min-w-full">
                        <thead>
                            <tr>
                                <th>Registration</th>
                                <th>Type</th>
                                <th>Brand / Model</th>
                                <th>Color</th>
                                <th>Purchased</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vehicles as $vehicle)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-sm font-black text-slate-700">
                                                {{ mb_strtoupper(mb_substr($vehicle->registration_number ?? 'V', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-extrabold text-slate-900">{{ $vehicle->registration_number }}</p>
                                                <p class="mt-1 text-xs font-medium text-slate-500">Fleet asset</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="inline-flex rounded-full bg-sky-50 px-3 py-1 text-xs font-extrabold text-sky-700">
                                            {{ $vehicle->type ?: 'Not set' }}
                                        </span>
                                    </td>
                                    <td>
                                        <p class="font-semibold text-slate-700">{{ $vehicle->brand ?: 'Brand not set' }}</p>
                                        <p class="mt-1 text-xs font-medium text-slate-500">{{ $vehicle->model ?: 'Model not set' }}</p>
                                    </td>
                                    <td class="font-semibold text-slate-600">{{ $vehicle->color ?: 'Not set' }}</td>
                                    <td class="font-semibold text-slate-600">
                                        {{ $vehicle->purchased_on ? \Illuminate\Support\Carbon::parse($vehicle->purchased_on)->format('d M Y') : 'Not set' }}
                                    </td>
                                    <td>
                                        <div class="flex justify-end gap-2">
                                            <a class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 transition hover:bg-emerald-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-emerald-100" href="{{ route('vehicles.edit', $vehicle) }}" title="Edit vehicle" aria-label="Edit {{ $vehicle->registration_number }}">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-700 transition hover:bg-rose-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-rose-100" onclick="return confirm('Delete this vehicle?')" title="Delete vehicle" aria-label="Delete {{ $vehicle->registration_number }}">
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
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17h14l-1.4-5.6A2 2 0 0015.66 10H8.34a2 2 0 00-1.94 1.4L5 17zM7 17a2 2 0 104 0M13 17a2 2 0 104 0"/>
                                                </svg>
                                            </div>
                                            <p class="text-base font-extrabold text-slate-900">No vehicles found</p>
                                            <p class="mt-1 text-sm font-medium text-slate-500">Create the first vehicle record to begin building the fleet directory.</p>
                                            <a href="{{ route('vehicles.create') }}" class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/15 transition hover:bg-emerald-700">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                                                </svg>
                                                Add Vehicle
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
