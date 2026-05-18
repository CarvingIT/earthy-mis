<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Equipment operations</p>
                <h2 class="text-3xl font-extrabold leading-tight text-slate-900">JCB Management</h2>
            </div>
            <p class="max-w-xl text-sm font-medium text-slate-500">Track JCB usage, monitor duration, and manage equipment operation logs.</p>
        </div>
    </x-slot>

    <style>
        * { box-sizing: border-box; }

        .jcb-shell {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f8f4 48%, #f8fafc 100%);
        }

        .jcb-panel,
        .jcb-table-card {
            border: 1px solid rgba(15, 118, 110, 0.12);
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(14px);
            box-shadow: 0 18px 48px rgba(15, 118, 110, 0.08), 0 2px 6px rgba(15, 23, 42, 0.04);
        }

        .jcb-table-card {
            overflow: hidden;
        }

        .jcb-table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .jcb-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .jcb-table thead th {
            padding: 14px 18px;
            text-align: left;
            font-size: 0.7rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #374151;
            background: linear-gradient(180deg, #f9fafb, #f3f4f6);
            border-bottom: 1px solid rgba(148, 163, 184, 0.35);
        }

        .jcb-table tbody td {
            padding: 14px 18px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.2);
            vertical-align: middle;
            font-size: 0.9rem;
            color: #1e293b;
        }

        .jcb-table tbody tr:last-child td {
            border-bottom: none;
        }

        .jcb-table tbody tr {
            transition: background 0.18s ease, transform 0.18s ease;
        }

        .jcb-table tbody tr:hover {
            background: rgba(16, 185, 129, 0.04);
            transform: translateY(-1px);
        }

        .jcb-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(16, 185, 129, 0.1);
            color: #065f46;
        }

        .btn-jcb-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #ffffff;
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 12px 24px rgba(16, 185, 129, 0.25);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .btn-jcb-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 32px rgba(16, 185, 129, 0.35);
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(148, 163, 184, 0.35);
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.18s ease;
        }

        .btn-icon.edit {
            color: #059669;
        }

        .btn-icon.edit:hover {
            background: rgba(16, 185, 129, 0.12);
            border-color: #10b981;
        }

        .btn-icon.delete {
            color: #dc2626;
        }

        .btn-icon.delete:hover {
            background: rgba(239, 68, 68, 0.12);
            border-color: #ef4444;
        }

        .alert-success {
            position: relative;
            padding: 12px 16px;
            border-radius: 0.75rem;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.25);
            color: #065f46;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .alert-close {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #065f46;
            cursor: pointer;
            font-size: 1.25rem;
            line-height: 1;
        }

        .empty-state {
            text-align: center;
            padding: 48px 24px;
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            color: #9ca3af;
        }

        .empty-state h3 {
            font-size: 1.125rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 24px;
        }

        @keyframes revealUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .reveal-up {
            animation: revealUp 0.45s ease both;
        }
    </style>

    <div class="py-8 jcb-shell">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Alert -->
            @if (session('success'))
                <div class="alert-success reveal-up" role="alert">
                    {{ session('success') }}
                    <button class="alert-close" onclick="this.parentElement.style.display='none'" aria-label="Dismiss">&times;</button>
                </div>
            @endif

            <!-- Header Actions -->
            <div class="flex justify-end reveal-up" style="animation-delay: 0.05s">
                <a href="{{ route('jcb.create') }}" class="btn-jcb-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Add JCB Entry
                </a>
            </div>

            <!-- Table Card -->
            <div class="jcb-table-card reveal-up" style="animation-delay: 0.1s">
                <div class="jcb-table-wrapper">
                    <table class="jcb-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Duration</th>
                                <th>Description</th>
                                <th style="width: 120px; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jcbs as $jcb)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ $jcb->Date }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="jcb-badge">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $jcb->duration }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-slate-600">{{ Str::limit($jcb->description, 50) }}</span>
                                    </td>
                                    <td style="text-align: right;">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('jcb.edit', $jcb) }}" class="btn-icon edit" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('jcb.destroy', $jcb) }}" onsubmit="return confirm('Delete this JCB entry?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-icon delete" title="Delete">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="empty-state">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <h3>No JCB entries recorded yet</h3>
                                            <p>Start tracking your JCB operations by adding your first entry.</p>
                                            <a href="{{ route('jcb.create') }}" class="btn-jcb-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                </svg>
                                                Add First JCB Entry
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('reveal-up');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.reveal-up').forEach(el => observer.observe(el));
        });
    </script>
</x-app-layout>
