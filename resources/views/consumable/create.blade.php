<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Inventory Management</p>
                <h2 class="text-3xl font-extrabold leading-tight text-slate-900">Add Consumable Item</h2>
            </div>
            <p class="max-w-xl text-sm font-medium text-slate-500">Register a new consumable item with name and optional description for your inventory.</p>
        </div>
    </x-slot>

    <style>
        * { box-sizing: border-box; }

        .consumable-form-shell {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f8f4 48%, #f8fafc 100%);
        }

        .form-panel,
        .form-section,
        .form-aside {
            border: 1px solid rgba(15, 23, 42, .08);
            background: #ffffff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
        }

        .form-section {
            position: relative;
            overflow: hidden;
        }

        .form-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(16, 185, 129, .08), transparent 42%);
            pointer-events: none;
        }

        .form-field {
            border: 1px solid #e2e8f0 !important;
            border-radius: .75rem !important;
            color: #334155;
            font-weight: 600;
            min-height: 2.85rem;
            transition: border-color .16s ease, box-shadow .16s ease;
        }

        .form-field:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, .14) !important;
        }

        .form-label {
            color: #334155;
            font-size: .8rem;
            font-weight: 800;
        }

        .section-mark {
            background: linear-gradient(135deg, #059669, #22d3ee);
            box-shadow: 0 12px 28px rgba(16, 185, 129, .25);
        }

        .reveal {
            opacity: 0;
            transform: translate3d(0, 18px, 0);
            transition: opacity .48s ease, transform .48s ease;
            transition-delay: var(--reveal-delay, 0ms);
        }

        .reveal.is-visible {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }

        @media (prefers-reduced-motion: reduce) {
            .reveal {
                opacity: 1;
                transform: none;
                transition: none;
            }
        }
    </style>

    <div class="consumable-form-shell min-h-screen py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="form-panel reveal rounded-2xl p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">New consumable item</h3>
                            <p class="text-sm font-medium text-slate-500">Add the item name and optional description details.</p>
                        </div>
                    </div>

                    <a href="{{ route('consumables.index') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-extrabold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100 sm:w-auto">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Items
                    </a>
                </div>
            </section>

            @if ($errors->any())
                <div class="reveal rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-bold text-rose-800 shadow-sm" data-dismissible-alert>
                    <div class="flex items-start justify-between gap-3">
                        <span>Please review the highlighted fields and try again.</span>
                        <button type="button" class="-mr-1 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-rose-700 transition hover:bg-rose-100 focus:outline-none focus:ring-4 focus:ring-rose-200" data-dismiss-alert aria-label="Dismiss alert">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('consumables.store') }}">
                @csrf

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_20rem]">
                    <div class="space-y-6">
                        <section class="form-section reveal rounded-2xl p-5 sm:p-6" style="--reveal-delay: 70ms;">
                            <div class="relative z-10">
                                <div class="mb-5 flex items-center gap-3">
                                    <div class="section-mark flex h-10 w-10 items-center justify-center rounded-xl text-white">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m0 10v-10l8 4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-extrabold text-slate-900">Item information</h3>
                                        <p class="text-sm font-medium text-slate-500">Basic details for the consumable item.</p>
                                    </div>
                                </div>

                                <div class="space-y-5">
                                    <div>
                                        <label for="item" class="form-label">Item Name <span class="text-rose-500">*</span></label>
                                        <x-text-input id="item" name="item" type="text" class="form-field mt-2 block w-full" :value="old('item')" required autofocus placeholder="e.g., Fuel, Oil, Grease" />
                                        <x-input-error class="mt-2" :messages="$errors->get('item')" />
                                    </div>

                                    <div>
                                        <label for="description" class="form-label">Description</label>
                                        <textarea id="description" name="description" class="form-field mt-2 block w-full resize-none" rows="5" placeholder="Add details about this consumable item (optional)...">{{ old('description') }}</textarea>
                                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                                        <p class="mt-2 text-xs font-medium text-slate-500">Provide any additional information about the item.</p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <aside class="space-y-6 lg:sticky lg:top-8 lg:h-fit">
                        <section class="form-section reveal rounded-2xl p-5" style="--reveal-delay: 140ms;">
                            <div class="relative z-10">
                                <p class="mb-4 text-sm font-bold text-slate-700">Actions</p>
                                <div class="flex flex-col gap-2">
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-extrabold text-white shadow-lg shadow-emerald-600/25 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Create Item
                                    </button>
                                    <a href="{{ route('consumables.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-extrabold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </section>

                        <section class="form-section reveal rounded-2xl p-5" style="--reveal-delay: 210ms;">
                            <div class="relative z-10">
                                <p class="mb-3 text-sm font-bold text-slate-700">Tips</p>
                                <ul class="space-y-2 text-xs font-medium text-slate-600">
                                    <li class="flex gap-2">
                                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>Use clear, descriptive item names</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>Descriptions help track usage patterns</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>Keep data consistent across items</span>
                                    </li>
                                </ul>
                            </div>
                        </section>
                    </aside>
                </div>
            </form>
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
