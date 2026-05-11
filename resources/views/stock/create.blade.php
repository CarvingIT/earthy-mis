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

        .form-panel {
            border: 1px solid rgba(15, 23, 42, .08);
            background: #ffffff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
            transition: box-shadow .18s ease, border-color .18s ease;
        }

        .form-panel:hover {
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

        .form-input {
            border: 1px solid #e2e8f0 !important;
            border-radius: .75rem !important;
            color: #334155;
            font-weight: 600;
            min-height: 2.65rem;
            outline: none;
            padding: .55rem .85rem !important;
            transition: border-color .16s ease, box-shadow .16s ease;
            width: 100%;
        }

        .form-input:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, .14);
        }

        .form-select {
            border: 1px solid #e2e8f0 !important;
            border-radius: .75rem !important;
            color: #334155;
            font-weight: 600;
            min-height: 2.65rem;
            outline: none;
            padding: .55rem .85rem !important;
            transition: border-color .16s ease, box-shadow .16s ease;
            width: 100%;
            background-color: white;
        }

        .form-select:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, .14);
        }

        .info-card {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 1px solid #a7f3d0;
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
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
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

            <section class="form-panel reveal rounded-2xl p-6 sm:p-8">
                <div class="mb-6 flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900">New stock transaction</h3>
                        <p class="mt-1 text-sm font-medium text-slate-500">Fill in the details below to add stock to your inventory.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('stock.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <x-input-label for="Date" value="Transaction Date" />
                            <x-text-input id="Date" name="Date" type="date" class="form-input mt-1 block w-full" :value="old('Date', now()->toDateString())" required />
                            <p class="mt-1 text-xs font-medium text-slate-500">When did this stock addition occur?</p>
                            <x-input-error class="mt-2" :messages="$errors->get('Date')" />
                        </div>

                        <div>
                            <x-input-label for="product_id" value="Product" />
                            <select name="product_id" id="product_id" class="form-select mt-1 block w-full" required>
                                <option value="">Select a product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            data-base-unit="{{ $product->baseUnit->name ?? 'N/A' }}"
                                            data-sales-unit="{{ $product->salesUnit->name ?? 'N/A' }}"
                                            {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs font-medium text-slate-500">Which product are you adding stock for?</p>
                            <x-input-error class="mt-2" :messages="$errors->get('product_id')" />
                        </div>
                    </div>

                    <div class="info-card rounded-xl p-4" id="product-info" style="display: none;">
                        <div class="flex items-start gap-3">
                            <svg class="h-5 w-5 shrink-0 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-emerald-900">Product Unit Information</p>
                                <p class="mt-1 text-xs font-medium text-emerald-800">
                                    Base Unit: <span id="base-unit-display" class="font-black">—</span> | 
                                    Sales Unit: <span id="sales-unit-display" class="font-black">—</span>
                                </p>
                                <p class="mt-2 text-xs font-semibold text-emerald-700" id="unit-instruction">Enter quantity in the base unit shown above.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <x-input-label for="quantity" value="Quantity" />
                            <x-text-input id="quantity" name="quantity" type="number" min="0.01" step="0.01" class="form-input mt-1 block w-full" :value="old('quantity')" placeholder="Enter quantity" required />
                            <p class="mt-1 text-xs font-medium text-slate-500" id="quantity-help">How much stock are you adding?</p>
                            <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                        </div>

                        <div>
                            <x-input-label for="unit_type" value="Unit Type" />
                            <select name="unit_type" id="unit_type" class="form-select mt-1 block w-full" required>
                                <option value="sales" {{ old('unit_type', 'sales') == 'sales' ? 'selected' : '' }}>Sales Unit</option>
                                <option value="base" {{ old('unit_type') == 'base' ? 'selected' : '' }}>Base Unit</option>
                            </select>
                            <p class="mt-1 text-xs font-medium text-slate-500" id="unit-type-help">Select which unit you're using for the quantity above.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('unit_type')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <x-input-label for="transaction_type" value="Transaction Type" />
                            <select name="transaction_type" id="transaction_type" class="form-select mt-1 block w-full" required>
                                <option value="">Select type</option>
                                <option value="purchase" {{ old('transaction_type') == 'purchase' ? 'selected' : '' }}>Purchase (New Stock)</option>
                                <option value="opening_stock" {{ old('transaction_type') == 'opening_stock' ? 'selected' : '' }}>Opening Stock (Initial)</option>
                                <option value="return" {{ old('transaction_type') == 'return' ? 'selected' : '' }}>Return (Customer Return)</option>
                                <option value="adjustment" {{ old('transaction_type') == 'adjustment' ? 'selected' : '' }}>Adjustment (Correction)</option>
                            </select>
                            <p class="mt-1 text-xs font-medium text-slate-500">What type of stock addition is this?</p>
                            <x-input-error class="mt-2" :messages="$errors->get('transaction_type')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="notes" value="Notes (Optional)" />
                        <textarea id="notes" name="notes" rows="3" class="form-input mt-1 block w-full" placeholder="Add any additional information about this transaction...">{{ old('notes') }}</textarea>
                        <p class="mt-1 text-xs font-medium text-slate-500">Optional: Add context like supplier name, purchase order number, or reason for adjustment.</p>
                        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
                        <a href="{{ route('stock.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-extrabold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-200">
                            Cancel
                        </a>
                        <x-primary-button class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/15 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save Stock
                        </x-primary-button>
                    </div>
                </form>
            </section>
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
