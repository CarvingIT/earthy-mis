<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Product operations</p>
                <h2 class="text-3xl font-extrabold leading-tight text-slate-900">Create Product</h2>
            </div>
            <p class="max-w-xl text-sm font-medium text-slate-500">Add a catalog item with pricing, unit setup, SKU, and description details.</p>
        </div>
    </x-slot>

    <style>
        * { box-sizing: border-box; }

        .product-form-shell {
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

    <div class="product-form-shell min-h-screen py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="form-panel reveal rounded-2xl p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l7 4v10l-7 4-7-4V7l7-4zM12 12l7-4M12 12v9M12 12L5 8"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">New catalog item</h3>
                            <p class="text-sm font-medium text-slate-500">Set the product name and price first; optional details can be completed later.</p>
                        </div>
                    </div>

                    <a href="{{ route('products.index') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-extrabold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100 sm:w-auto">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Products
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

            <form method="POST" action="{{ route('products.store') }}">
                @csrf

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_20rem]">
                    <div class="space-y-6">
                        <section class="form-section reveal rounded-2xl p-5 sm:p-6" style="--reveal-delay: 70ms;">
                            <div class="relative z-10">
                                <div class="mb-5 flex items-center gap-3">
                                    <div class="section-mark flex h-10 w-10 items-center justify-center rounded-xl text-white">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h6M5 3h14a1 1 0 011 1v16l-4-2-4 2-4-2-4 2V4a1 1 0 011-1z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-extrabold text-slate-900">Catalog details</h3>
                                        <p class="text-sm font-medium text-slate-500">Product name, SKU, and searchable identity.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                    <div>
                                        <label for="name" class="form-label">Name <span class="text-rose-500">*</span></label>
                                        <x-text-input id="name" name="name" type="text" class="form-field mt-2 block w-full" :value="old('name')" required autofocus placeholder="Product name" />
                                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                    </div>

                                    <div>
                                        <label for="sku" class="form-label">SKU</label>
                                        <x-text-input id="sku" name="sku" type="text" class="form-field mt-2 block w-full" :value="old('sku')" placeholder="SKU-001" />
                                        <x-input-error class="mt-2" :messages="$errors->get('sku')" />
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="form-section reveal rounded-2xl p-5 sm:p-6" style="--reveal-delay: 120ms;">
                            <div class="relative z-10">
                                <div class="mb-5 flex items-center gap-3">
                                    <div class="section-mark flex h-10 w-10 items-center justify-center rounded-xl text-white">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2 0-3 .9-3 2s1 2 3 2 3 .9 3 2-1 2-3 2m0-8V6m0 12v-2M5 12a7 7 0 1114 0 7 7 0 01-14 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-extrabold text-slate-900">Pricing and unit</h3>
                                        <p class="text-sm font-medium text-slate-500">Selling price and base measurement unit.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                    <div>
                                        <label for="price" class="form-label">Price <span class="text-rose-500">*</span></label>
                                        <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="form-field mt-2 block w-full" :value="old('price')" required placeholder="0.00" />
                                        <x-input-error class="mt-2" :messages="$errors->get('price')" />
                                    </div>

                                    <div>
                                        <label for="base_unit_id" class="form-label">Base unit</label>
                                        <select id="base_unit_id" name="base_unit_id" class="form-field mt-2 block w-full px-4 py-2.5 shadow-sm">
                                            <option value="">Select Base Unit</option>
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}" {{ old('base_unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('base_unit_id')" />
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="form-section reveal rounded-2xl p-5 sm:p-6" style="--reveal-delay: 150ms;">
                            <div class="relative z-10">
                                <div class="mb-5 flex items-center gap-3">
                                    <div class="section-mark flex h-10 w-10 items-center justify-center rounded-xl text-white">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10M4 18h8"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-extrabold text-slate-900">Description</h3>
                                        <p class="text-sm font-medium text-slate-500">Notes that help identify or sell this product.</p>
                                    </div>
                                </div>

                                <div>
                                    <label for="description" class="form-label">Description</label>
                                    <textarea id="description" name="description" rows="4" class="form-field mt-2 block w-full px-4 py-3 shadow-sm" placeholder="Product details, usage notes, or catalog description">{{ old('description') }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                                </div>
                            </div>
                        </section>
                    </div>

                    <aside class="form-aside reveal h-fit rounded-2xl p-5" style="--reveal-delay: 170ms;">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Ready to save</h3>
                        <p class="mt-2 text-sm font-medium leading-6 text-slate-500">Product name and price are required. SKU, unit, and description can be added now or updated later.</p>

                        <div class="mt-6 space-y-3">
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/15 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Product
                            </button>
                            <a href="{{ route('products.index') }}" class="inline-flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-extrabold text-slate-700 transition hover:bg-slate-50">
                                Cancel
                            </a>
                        </div>
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
