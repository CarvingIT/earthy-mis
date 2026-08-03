<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Master Data</p>
                <h2 class="text-3xl font-extrabold leading-tight text-slate-900">Leads</h2>
            </div>
            <p class="max-w-xl text-sm font-medium text-slate-500">Manage and track lead inquiries, society contacts, and details.</p>
        </div>
    </x-slot>

    <style>
        * { box-sizing: border-box; }
        .leads-shell { background: linear-gradient(180deg, #f8fafc 0%, #f1f8f4 48%, #f8fafc 100%); }
        .leads-panel, .leads-stat, .leads-table-card {
            border: 1px solid rgba(15, 23, 42, .08);
            background: #ffffff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
        }
        .leads-stat { position: relative; overflow: hidden; transition: box-shadow .18s ease, border-color .18s ease; }
        .leads-stat::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, var(--stat-tint), transparent 50%); opacity: .95; pointer-events: none; }
        .leads-stat::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: var(--stat-accent); }
        .stat-icon { background: var(--stat-accent); box-shadow: 0 12px 28px var(--stat-shadow); }
        .stat-value { color: var(--stat-text); }
        .reveal { opacity: 0; transform: translate3d(0, 18px, 0); transition: opacity .48s ease, transform .48s ease; transition-delay: var(--reveal-delay, 0ms); }
        .reveal.is-visible { opacity: 1; transform: translate3d(0, 0, 0); }
        .leads-table-wrap { overflow-x: auto; }
        .leads-table { border-collapse: separate !important; border-spacing: 0; }
        .leads-table thead th { border-bottom: 1px solid rgba(15, 23, 42, .08) !important; background: #f8fafc !important; color: #64748b !important; font-size: .72rem; font-weight: 800 !important; letter-spacing: .08em; padding: .9rem 1rem !important; text-transform: uppercase; white-space: nowrap; }
        .leads-table tbody td { border-bottom: 1px solid rgba(15, 23, 42, .06); color: #334155; font-size: .875rem; padding: 1rem !important; vertical-align: top; }
        .leads-table tbody tr:hover { background: #f8fafc; }
        .leads-table-card .dt-input { border: 1px solid #e2e8f0 !important; border-radius: .75rem !important; outline: none; padding: .55rem .85rem !important; }
        .leads-table-card .dt-paging .dt-paging-button { border: 1px solid #e2e8f0 !important; border-radius: .65rem !important; margin-left: .25rem; padding: .45rem .75rem !important; }
        .leads-table-card .dt-paging .dt-paging-button.current, .leads-table-card .dt-paging .dt-paging-button:hover { background: #0f172a !important; color: #ffffff !important; }
    </style>

    @php
        $totalLeads = $leads->count();
        $leadsWithContact = $leads->filter(fn($l) => filled($l->contact_number) || filled($l->contact_name))->count();
        $stats = [
            [
                'label' => 'Total Leads',
                'value' => number_format($totalLeads),
                'note' => 'Registered society leads',
                'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                'style' => '--stat-accent: linear-gradient(135deg, #10b981, #34d399); --stat-tint: rgba(16, 185, 129, .16); --stat-shadow: rgba(16, 185, 129, .3); --stat-text: #047857;',
            ],
            [
                'label' => 'With Contact Info',
                'value' => number_format($leadsWithContact),
                'note' => 'Leads with contact name/phone',
                'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
                'style' => '--stat-accent: linear-gradient(135deg, #0284c7, #22d3ee); --stat-tint: rgba(14, 165, 233, .15); --stat-shadow: rgba(14, 165, 233, .3); --stat-text: #0369a1;',
            ],
        ];
    @endphp

    <div class="leads-shell min-h-screen py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="leads-panel reveal rounded-2xl p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Leads Management</h3>
                            <p class="text-sm font-medium text-slate-500">Track and manage society leads, contacts, and inquiries.</p>
                        </div>
                    </div>

                    <a href="{{ route('leads.create') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/15 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200 sm:w-auto">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                        </svg>
                        Add Lead
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

            <section class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach ($stats as $index => $stat)
                    <article class="leads-stat reveal rounded-2xl p-5" style="{{ $stat['style'] }} --reveal-delay: {{ $index * 70 }}ms;">
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

            <section class="leads-table-card reveal rounded-2xl p-4 sm:p-6">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">All Leads</h3>
                        <p class="mt-1 text-sm font-medium text-slate-500">List of society leads and their contact details.</p>
                    </div>
                    <span class="w-fit rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-emerald-700">
                        {{ number_format($totalLeads) }} leads
                    </span>
                </div>

                <div class="leads-table-wrap">
                    <table id="leads-table" {{ $totalLeads > 0 ? 'data-datatable' : '' }} class="leads-table min-w-full">
                        <thead>
                            <tr>
                                <th>Society Name</th>
                                <th>Contact Name</th>
                                <th>Contact Number</th>
                                <th>Details</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($leads as $lead)
                                <tr>
                                    <td>
                                        <div class="flex items-start gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-sm font-black text-emerald-800">
                                                {{ mb_strtoupper(mb_substr($lead->society_name ?? 'L', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-extrabold text-slate-900">{{ $lead->society_name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="font-medium text-slate-700">{{ $lead->contact_name ?: '-' }}</span>
                                    </td>
                                    <td>
                                        @if ($lead->contact_number)
                                            <a href="tel:{{ $lead->contact_number }}" class="font-semibold text-emerald-700 hover:underline">{{ $lead->contact_number }}</a>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (filled($lead->details))
                                            <p class="text-sm font-medium text-slate-600">{{ Str::limit($lead->details, 60) }}</p>
                                        @else
                                            <p class="text-sm font-medium text-slate-400">No details</p>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex justify-end gap-2">
                                            <a class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 transition hover:bg-emerald-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-emerald-100" href="{{ route('leads.edit', $lead) }}" title="Edit Lead">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('leads.destroy', $lead) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-700 transition hover:bg-rose-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-rose-100" onclick="return confirm('Delete this lead?')" title="Delete Lead">
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
                                    <td colspan="5">
                                        <div class="flex flex-col items-center justify-center py-12 text-center">
                                            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                            </div>
                                            <p class="text-base font-extrabold text-slate-900">No leads found</p>
                                            <p class="mt-1 text-sm font-medium text-slate-500">Create your first lead entry to start tracking.</p>
                                            <a href="{{ route('leads.create') }}" class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-lg transition hover:bg-emerald-700">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                                                </svg>
                                                Add Lead
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
