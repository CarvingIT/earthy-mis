<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Community operations</p>
                <h2 class="text-3xl font-extrabold leading-tight text-slate-900">Societies</h2>
            </div>
            <p class="max-w-xl text-sm font-medium text-slate-500">Manage society profiles, contacts, joining details, and service reach from one organized workspace.</p>
        </div>
    </x-slot>

    <style>
        * { box-sizing: border-box; }

        .societies-shell {
            background:
                linear-gradient(180deg, #f8fafc 0%, #f1f8f4 48%, #f8fafc 100%);
        }

        .society-panel,
        .society-stat,
        .society-table-card {
            border: 1px solid rgba(15, 23, 42, .08);
            background: #ffffff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
        }

        .society-stat {
            position: relative;
            overflow: hidden;
            transition: box-shadow .18s ease, border-color .18s ease;
        }

        .society-stat::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--stat-tint), transparent 50%);
            opacity: .95;
            pointer-events: none;
        }

        .society-stat::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--stat-accent);
        }

        .society-stat:hover,
        .society-table-card:hover {
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

        .societies-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .societies-table {
            border-collapse: separate !important;
            border-spacing: 0;
        }

        .societies-table thead th {
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

        .societies-table tbody td {
            border-bottom: 1px solid rgba(15, 23, 42, .06);
            color: #334155;
            font-size: .875rem;
            padding: 1rem !important;
            vertical-align: top;
        }

        .societies-table tbody tr {
            transition: background-color .16s ease;
        }

        .societies-table tbody tr:hover {
            background: #f8fafc;
        }

        .societies-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .societies-table-card .dt-container {
            color: #475569;
            font-size: .875rem;
        }

        .societies-table-card .dt-layout-row {
            align-items: center;
            gap: 1rem;
            margin: 0 0 1rem;
        }

        .societies-table-card .dt-layout-row:last-child {
            margin: 1rem 0 0;
        }

        .societies-table-card .dt-search label,
        .societies-table-card .dt-info {
            color: #64748b;
            font-size: .82rem;
            font-weight: 700;
        }

        .societies-table-card .dt-input {
            border: 1px solid #e2e8f0 !important;
            border-radius: .75rem !important;
            color: #334155;
            font-weight: 600;
            min-height: 2.65rem;
            outline: none;
            padding: .55rem .85rem !important;
            transition: border-color .16s ease, box-shadow .16s ease;
        }

        .societies-table-card .dt-input:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, .14);
        }

        .societies-table-card .dt-paging .dt-paging-button {
            border: 1px solid #e2e8f0 !important;
            border-radius: .65rem !important;
            color: #475569 !important;
            font-weight: 800;
            margin-left: .25rem;
            padding: .45rem .75rem !important;
        }

        .societies-table-card .dt-paging .dt-paging-button.current,
        .societies-table-card .dt-paging .dt-paging-button:hover {
            background: #0f172a !important;
            color: #ffffff !important;
        }

        @media (max-width: 640px) {
            .societies-table-card .dt-layout-row {
                align-items: stretch;
                flex-direction: column;
            }

            .societies-table-card .dt-search,
            .societies-table-card .dt-search input {
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
        $totalSocieties = $societies->count();
        $activeCities = $societies->pluck('city')->filter()->map(fn ($city) => trim($city))->filter()->unique(fn ($city) => mb_strtolower($city))->count();
        $totalHomes = $societies->sum(function ($society) {
            return (int) preg_replace('/[^\d]/', '', (string) $society->flats_families);
        });
        $contactCoverage = $totalSocieties > 0
            ? round(($societies->filter(fn ($society) => filled($society->phone) || filled($society->contact_person_email))->count() / $totalSocieties) * 100)
            : 0;

        $stats = [
            [
                'label' => 'Total Societies',
                'value' => number_format($totalSocieties),
                'note' => 'Registered communities',
                'icon' => 'M3 21h18M5 21V7l8-4 8 4v14M9 21v-8h8v8M9 10h.01M15 10h.01',
                'style' => '--stat-accent: linear-gradient(135deg, #0284c7, #22d3ee); --stat-tint: rgba(14, 165, 233, .15); --stat-shadow: rgba(14, 165, 233, .3); --stat-text: #0369a1;',
            ],
            [
                'label' => 'Cities Covered',
                'value' => number_format($activeCities),
                'note' => 'Unique operating cities',
                'icon' => 'M12 21s7-4.438 7-11a7 7 0 10-14 0c0 6.562 7 11 7 11zM12 13a3 3 0 100-6 3 3 0 000 6z',
                'style' => '--stat-accent: linear-gradient(135deg, #059669, #84cc16); --stat-tint: rgba(16, 185, 129, .16); --stat-shadow: rgba(16, 185, 129, .3); --stat-text: #047857;',
            ],
            [
                'label' => 'Flats / Families',
                'value' => number_format($totalHomes),
                'note' => 'Approximate homes served',
                'icon' => 'M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m8-4a4 4 0 10-8 0 4 4 0 008 0zm6 0a3 3 0 10-6 0 3 3 0 006 0z',
                'style' => '--stat-accent: linear-gradient(135deg, #f59e0b, #f97316); --stat-tint: rgba(245, 158, 11, .16); --stat-shadow: rgba(245, 158, 11, .3); --stat-text: #b45309;',
            ],
            [
                'label' => 'Contact Coverage',
                'value' => $contactCoverage . '%',
                'note' => 'With phone or email',
                'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.95.68l1.5 4.49a1 1 0 01-.5 1.21l-2.26 1.13a11.04 11.04 0 005.52 5.52l1.13-2.26a1 1 0 011.21-.5l4.49 1.5a1 1 0 01.68.95V19a2 2 0 01-2 2h-1C9.72 21 3 14.28 3 6V5z',
                'style' => '--stat-accent: linear-gradient(135deg, #4f46e5, #06b6d4); --stat-tint: rgba(99, 102, 241, .14); --stat-shadow: rgba(79, 70, 229, .28); --stat-text: #4338ca;',
            ],
        ];
    @endphp

    <div class="societies-shell min-h-screen py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="society-panel reveal rounded-2xl p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6M3 21h18M5 21V7l7-4 7 4v14"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Society directory</h3>
                            <p class="text-sm font-medium text-slate-500">Use search and sorting to find contacts, cities, and onboarding records quickly.</p>
                        </div>
                    </div>

                    <a href="{{ route('societies.create') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/15 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200 sm:w-auto">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                        </svg>
                        Add Society
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
                    <article class="society-stat reveal rounded-2xl p-5" style="{{ $stat['style'] }} --reveal-delay: {{ $index * 70 }}ms;">
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

            <section class="society-table-card reveal rounded-2xl p-4 sm:p-6">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">All societies</h3>
                        <p class="mt-1 text-sm font-medium text-slate-500">Primary contacts, city coverage, and onboarding information.</p>
                    </div>
                    <span class="w-fit rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-emerald-700">
                        {{ number_format($totalSocieties) }} records
                    </span>
                </div>

                <div class="societies-table-wrap">
                    <table id="societies-table" data-datatable class="societies-table min-w-full">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>City</th>
                                <th>Joining Month</th>
                                <th>Flats/Families</th>
                                <th>Chairman</th>
                                <th>Secretary / Email</th>
                                <th>Phone</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($societies as $society)
                                <tr>
                                    <td>
                                        <div class="flex items-start gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-sm font-black text-slate-700">
                                                {{ mb_strtoupper(mb_substr($society->name ?? 'S', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-extrabold text-slate-900">{{ $society->name }}</p>
                                                @if (filled($society->address))
                                                    <p class="mt-1 max-w-xs text-xs font-medium leading-5 text-slate-500">{{ $society->address }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="inline-flex rounded-full bg-sky-50 px-3 py-1 text-xs font-extrabold text-sky-700">
                                            {{ $society->city ?: 'Not set' }}
                                        </span>
                                    </td>
                                    <td class="font-semibold text-slate-600">{{ $society->joining_month ?: 'Not set' }}</td>
                                    <td>
                                        <span class="font-extrabold text-slate-900">{{ $society->flats_families ?: '0' }}</span>
                                    </td>
                                    <td class="font-semibold text-slate-700">{{ $society->chairman_name ?: 'Not set' }}</td>
                                    <td>
                                        <p class="font-semibold text-slate-700">{{ $society->secretary_name ?: 'Not set' }}</p>
                                        @if (filled($society->contact_person_email))
                                            <a href="mailto:{{ $society->contact_person_email }}" class="mt-1 inline-block text-xs font-bold text-emerald-700 hover:text-emerald-800">{{ $society->contact_person_email }}</a>
                                        @else
                                            <p class="mt-1 text-xs font-medium text-slate-400">Email not set</p>
                                        @endif
                                    </td>
                                    <td>
                                        @if (filled($society->phone))
                                            <a href="tel:{{ $society->phone }}" class="font-bold text-slate-700 hover:text-emerald-700">{{ $society->phone }}</a>
                                        @else
                                            <span class="text-slate-400">Not set</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex justify-end gap-2">
                                            <a class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 transition hover:bg-emerald-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-emerald-100" href="{{ route('societies.edit', $society) }}" title="Edit society" aria-label="Edit {{ $society->name }}">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('societies.destroy', $society) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-700 transition hover:bg-rose-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-rose-100" onclick="return confirm('Delete this society?')" title="Delete society" aria-label="Delete {{ $society->name }}">
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
                                    <td colspan="8">
                                        <div class="flex flex-col items-center justify-center py-12 text-center">
                                            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-7h6v7"/>
                                                </svg>
                                            </div>
                                            <p class="text-base font-extrabold text-slate-900">No societies found</p>
                                            <p class="mt-1 text-sm font-medium text-slate-500">Create the first society profile to begin building the directory.</p>
                                            <a href="{{ route('societies.create') }}" class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/15 transition hover:bg-emerald-700">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                                                </svg>
                                                Add Society
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
