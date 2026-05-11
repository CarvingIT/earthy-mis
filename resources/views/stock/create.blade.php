<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Stock operations</p>
                <h2 class="text-3xl font-extrabold leading-tight text-slate-900">Add Stock</h2>
            </div>
            <p class="max-w-xl text-sm font-medium text-slate-500">Record new stock additions, purchases, or adjustments for your products.</p>
        </div>
    </x-slot>

    <style>
        * { box-sizing: border-box; }

        .stock-form-shell {
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

        .unit-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.025em;
        }

        .badge-base {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .badge-sales {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .quick-tip {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border-left: 3px solid #10b981;
            padding: 0.75rem 1rem;
            border-radius: 0 0.5rem 0.5rem 0;
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

    <div class="stock-form-shell min-h-screen py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="form-panel reveal rounded-2xl p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">New stock transaction</h3>
                            <p class="text-sm font-medium text-slate-500">Record stock additions, purchases, or adjustments for your inventory.</p>
                        </div>
                    </div>

                    <a href="{{ route('stock.index') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-extrabold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100 sm:w-auto">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Stock
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

            <form method="POST" action="{{ route('stock.store') }}">
                @csrf

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_20rem]">
                    <div class="space-y-6">
                        <!-- Main Form Card - Unified -->
                        <section class="form-panel reveal rounded-2xl p-6 sm:p-8" style="--reveal-delay: 70ms;">
                            <div class="mb-6">
                                <h3 class="text-xl font-extrabold text-slate-900">Stock Entry Details</h3>
                                <p class="mt-1 text-sm font-medium text-slate-500">Fill in the details below to add stock to your inventory.</p>
                            </div>

                            <!-- Group 1: When & What -->
                            <div class="space-y-5">
                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                    <div>
                                        <label for="Date" class="form-label">Transaction date <span class="text-rose-500">*</span></label>
                                        <x-text-input id="Date" name="Date" type="date" class="form-field mt-2 block w-full" :value="old('Date', now()->toDateString())" required />
                                        <p class="mt-1 text-xs font-medium text-slate-500">When did this stock addition happen?</p>
                                        <x-input-error class="mt-2" :messages="$errors->get('Date')" />
                                    </div>

                                    <div>
                                        <label for="product_id" class="form-label">Product <span class="text-rose-500">*</span></label>
                                        <select name="product_id" id="product_id" class="form-field mt-2 block w-full px-4 py-2.5 shadow-sm" required>
                                            <option value="">— Select a product —</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" 
                                                        data-base-unit="{{ $product->baseUnit->name ?? 'N/A' }}"
                                                        data-sales-unit="{{ $product->salesUnit->name ?? 'N/A' }}"
                                                        {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-xs font-medium text-slate-500">Which product needs more stock?</p>
                                        <x-input-error class="mt-2" :messages="$errors->get('product_id')" />
                                    </div>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="my-6 h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>

                            <!-- Group 2: How Much -->
                            <div class="space-y-5">
                                <div class="quick-tip mb-4 rounded-lg">
                                    <p class="text-xs font-semibold text-emerald-900">💡 Quick tip:</p>
                                    <p class="mt-1 text-xs font-medium text-emerald-800">Select a product first to see its available units. You can enter quantity in either base or sales unit—we convert automatically!</p>
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                    <div>
                                        <label for="quantity" class="form-label">Quantity <span class="text-rose-500">*</span></label>
                                        <x-text-input id="quantity" name="quantity" type="number" min="0.01" step="0.01" class="form-field mt-2 block w-full" :value="old('quantity')" placeholder="e.g., 100" required />
                                        <p class="mt-1 text-xs font-medium text-slate-500" id="quantity-help">How much are you adding?</p>
                                        <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                                    </div>

                                    <div>
                                        <label for="unit_type" class="form-label">I'm entering in... <span class="text-rose-500">*</span></label>
                                        <select name="unit_type" id="unit_type" class="form-field mt-2 block w-full px-4 py-2.5 shadow-sm" required>
                                            <option value="sales" {{ old('unit_type', 'sales') == 'sales' ? 'selected' : '' }}>📦 Sales Unit (what customers buy)</option>
                                            <option value="base" {{ old('unit_type') == 'base' ? 'selected' : '' }}>⚖️ Base Unit (how we track stock)</option>
                                        </select>
                                        <p class="mt-1 text-xs font-medium text-slate-500" id="unit-type-help">Choose which unit matches your quantity above.</p>
                                        <x-input-error class="mt-2" :messages="$errors->get('unit_type')" />
                                    </div>
                                </div>

                                <!-- Dynamic Product Info -->
                                <div class="rounded-xl border-2 border-emerald-300 bg-gradient-to-br from-emerald-50 to-teal-50 px-5 py-4 shadow-sm" id="product-info" style="display: none;">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-bold text-emerald-900">This product uses:</p>
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                <span class="unit-badge badge-base">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Base: <span id="base-unit-display" class="font-black">—</span>
                                                </span>
                                                <span class="unit-badge badge-sales">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                                    </svg>
                                                    Sales: <span id="sales-unit-display" class="font-black">—</span>
                                                </span>
                                            </div>
                                            <p class="mt-3 text-xs font-semibold text-emerald-800" id="unit-instruction">✨ Choose your preferred unit above. The system converts automatically!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="my-6 h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>

                            <!-- Group 3: Why & Notes -->
                            <div class="space-y-5">
                                <div>
                                    <label for="transaction_type" class="form-label">Why are you adding stock? <span class="text-rose-500">*</span></label>
                                    <select name="transaction_type" id="transaction_type" class="form-field mt-2 block w-full px-4 py-2.5 shadow-sm" required>
                                        <option value="">— Select the reason —</option>
                                        <option value="purchase" {{ old('transaction_type') == 'purchase' ? 'selected' : '' }}>🛒 Purchase (bought new stock from supplier)</option>
                                        <option value="opening_stock" {{ old('transaction_type') == 'opening_stock' ? 'selected' : '' }}>📋 Opening Stock (initial inventory setup)</option>
                                        <option value="return" {{ old('transaction_type') == 'return' ? 'selected' : '' }}>↩️ Return (customer returned products)</option>
                                        <option value="adjustment" {{ old('transaction_type') == 'adjustment' ? 'selected' : '' }}>🔧 Adjustment (correcting inventory count)</option>
                                    </select>
                                    <p class="mt-1 text-xs font-medium text-slate-500">What's the source of this stock?</p>
                                    <x-input-error class="mt-2" :messages="$errors->get('transaction_type')" />
                                </div>

                                <div>
                                    <label for="notes" class="form-label">Additional notes (Optional)</label>
                                    <textarea id="notes" name="notes" rows="3" class="form-field mt-2 block w-full px-4 py-3 shadow-sm" placeholder="Examples: Supplier name, PO number, batch details...">{{ old('notes') }}</textarea>
                                    <p class="mt-1 text-xs font-medium text-slate-500">💬 Helpful for tracking: supplier info, purchase orders, or special circumstances.</p>
                                    <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                                </div>
                            </div>

                            <!-- Bottom Save Buttons -->
                            <div class="mt-8 flex flex-col gap-3 border-t-2 border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-between">
                                <div class="order-2 sm:order-1">
                                    <a href="{{ route('stock.index') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border-2 border-slate-200 bg-white px-5 py-3 text-sm font-extrabold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 sm:w-auto">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Cancel
                                    </a>
                                </div>
                                <div class="order-1 sm:order-2">
                                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-emerald-500/30 transition-all hover:from-emerald-700 hover:to-emerald-600 hover:shadow-emerald-600/40 focus:outline-none focus:ring-4 focus:ring-emerald-200 sm:w-auto">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Save Stock Entry
                                    </button>
                                </div>
                            </div>
                        </section>
                    </div>

                    <aside class="form-aside reveal h-fit rounded-2xl p-5" style="--reveal-delay: 170ms;">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Ready to save?</h3>
                        <p class="mt-2 text-sm font-medium leading-6 text-slate-500">Make sure all required fields (marked with <span class="text-rose-500 font-black">*</span>) are filled. Double-check the quantity and unit before saving.</p>

                        <div class="mt-6 space-y-3">
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-slate-900 to-slate-800 px-5 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-slate-900/20 transition-all hover:from-emerald-700 hover:to-emerald-600 hover:shadow-emerald-500/30 focus:outline-none focus:ring-4 focus:ring-emerald-200">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Stock Entry
                            </button>
                            <a href="{{ route('stock.index') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border-2 border-slate-200 bg-white px-5 py-3 text-sm font-extrabold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancel
                            </a>
                        </div>

                        <div class="mt-6 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-xs font-bold text-slate-700">📌 Remember:</p>
                            <ul class="mt-2 space-y-1 text-xs font-medium text-slate-600">
                                <li>• Stock is always tracked in base units</li>
                                <li>• Sales units convert automatically</li>
                                <li>• You can edit transactions later</li>
                            </ul>
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
                
                // Handle dismissible alerts
                document.querySelectorAll('[data-dismiss-alert]').forEach((button) => {
                    button.addEventListener('click', () => {
                        button.closest('[data-dismissible-alert]')?.remove();
                    });
                });

                // Reveal animations
                if ('IntersectionObserver' in window) {
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach((entry) => {
                            if (!entry.isIntersecting) return;
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        });
                    }, { threshold: .12 });

                    revealItems.forEach((item) => observer.observe(item));
                } else {
                    revealItems.forEach((item) => item.classList.add('is-visible'));
                }

                // Product selection handler
                const productSelect = document.getElementById('product_id');
                const productInfo = document.getElementById('product-info');
                const baseUnitDisplay = document.getElementById('base-unit-display');
                const salesUnitDisplay = document.getElementById('sales-unit-display');
                const quantityLabel = document.querySelector('label[for="quantity"]');
                const quantityHelp = document.getElementById('quantity-help');
                const unitInstruction = document.getElementById('unit-instruction');
                const unitTypeSelect = document.getElementById('unit_type');
                const unitTypeHelp = document.getElementById('unit-type-help');

                if (productSelect) {
                    function updateLabels() {
                        const selectedOption = productSelect.options[productSelect.selectedIndex];
                        
                        if (productSelect.value) {
                            const baseUnit = selectedOption.dataset.baseUnit || 'units';
                            const salesUnit = selectedOption.dataset.salesUnit || 'units';
                            
                            baseUnitDisplay.textContent = baseUnit;
                            salesUnitDisplay.textContent = salesUnit;
                            
                            // Update labels based on selected unit type
                            const unitType = unitTypeSelect ? unitTypeSelect.value : 'base';
                            
                            if (unitType === 'sales') {
                                if (quantityLabel) {
                                    quantityLabel.textContent = `Quantity (in ${salesUnit})`;
                                }
                                if (quantityHelp) {
                                    quantityHelp.textContent = `How many ${salesUnit} are you adding?`;
                                }
                                if (unitTypeHelp) {
                                    unitTypeHelp.textContent = `You're entering quantity in ${salesUnit}. System will convert to ${baseUnit} for stock tracking.`;
                                }
                            } else {
                                if (quantityLabel) {
                                    quantityLabel.textContent = `Quantity (in ${baseUnit})`;
                                }
                                if (quantityHelp) {
                                    quantityHelp.textContent = `How much stock are you adding (in ${baseUnit})?`;
                                }
                                if (unitTypeHelp) {
                                    unitTypeHelp.textContent = `You're entering quantity directly in ${baseUnit} (base unit).`;
                                }
                            }
                            
                            if (unitInstruction) {
                                unitInstruction.textContent = `Choose whether to enter quantity in ${baseUnit} (base) or ${salesUnit} (sales). Conversion happens automatically.`;
                            }
                            
                            productInfo.style.display = 'block';
                        } else {
                            productInfo.style.display = 'none';
                        }
                    }

                    productSelect.addEventListener('change', updateLabels);
                    
                    if (unitTypeSelect) {
                        unitTypeSelect.addEventListener('change', updateLabels);
                    }

                    // Trigger on page load if product is pre-selected
                    if (productSelect.value) {
                        updateLabels();
                    }
                }
            });
        </script>
    @endpush
</x-app-layout>
