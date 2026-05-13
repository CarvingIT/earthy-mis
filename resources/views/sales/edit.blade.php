<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Sales operations</p>
                <h2 class="text-3xl font-extrabold leading-tight text-slate-900">Edit Sale</h2>
            </div>
            <p class="max-w-xl text-sm font-medium text-slate-500">Update sale transaction details and correct any information.</p>
        </div>
    </x-slot>

    <style>
        * { box-sizing: border-box; }

        .sales-form-shell {
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

    <script>
        function formatAmount(value) {
            return Number.isFinite(value) ? value.toFixed(2) : '';
        }

        function formatMoney(value) {
            return Number.isFinite(value) ? `Rs. ${value.toFixed(2)}` : 'Rs. 0.00';
        }

        function renderPricingBreakdown(data) {
            const panel = document.getElementById('pricing-breakdown');
            if (!panel) {
                return;
            }

            if (!data || data.rate === undefined) {
                panel.classList.add('hidden');
                return;
            }

            const baseRate = parseFloat(data.base_rate);
            const salesRate = parseFloat(data.rate);
            const salesUnitName = data.sales_unit_name || 'sales unit';
            const baseUnitName = data.base_unit_name || 'base unit';
            const salesUnitQuantity = parseFloat(data.sales_unit_quantity);

            panel.classList.remove('hidden');
            panel.querySelector('[data-base-rate]').textContent = `${formatMoney(baseRate)} / ${baseUnitName}`;
            panel.querySelector('[data-conversion]').textContent = Number.isFinite(salesUnitQuantity) && salesUnitQuantity > 1
                ? `1 ${salesUnitName} = ${salesUnitQuantity} ${baseUnitName}`
                : `1 ${salesUnitName} = 1 ${baseUnitName}`;
            panel.querySelector('[data-sales-rate]').textContent = `${formatMoney(salesRate)} / ${salesUnitName}`;
            panel.querySelector('[data-formula]').textContent = Number.isFinite(salesUnitQuantity)
                ? `${formatMoney(baseRate)} × ${salesUnitQuantity} = ${formatMoney(salesRate)}`
                : `${formatMoney(baseRate)} × conversion = ${formatMoney(salesRate)}`;
        }

        function calculateAmount(){
            const quantity = parseFloat(document.getElementById('quantity').value);
            const rate = parseFloat(document.getElementById('rate').value);

            if (Number.isNaN(quantity) || Number.isNaN(rate)) {
                return;
            }

            document.getElementById('amount').value = formatAmount(quantity * rate);
        }

        function getRate(productId){
            if (!productId) {
                document.getElementById('rate').value = '';
                document.getElementById('amount').value = '';
                renderPricingBreakdown(null);
                return;
            }

            fetch('/get_product_rate/ajax/' + productId)
                .then(response => response.json())
                .then(data => {
                    if (data.rate !== undefined) {
                        document.getElementById('rate').value = data.rate;
                        renderPricingBreakdown(data);
                        calculateAmount();
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        document.addEventListener('DOMContentLoaded', () => {
            const productSelect = document.getElementById('product_id');
            if (productSelect && productSelect.value) {
                getRate(productSelect.value);
            }
        });
    </script>

    <div class="sales-form-shell min-h-screen py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="form-panel reveal rounded-2xl p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Edit sales transaction</h3>
                            <p class="text-sm font-medium text-slate-500">Update sale transaction details and correct any information.</p>
                        </div>
                    </div>

                    <a href="{{ route('sale.index') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-extrabold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100 sm:w-auto">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Sales
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

            @if (session('success'))
                <div class="reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800 shadow-sm" data-dismissible-alert>
                    <div class="flex items-start justify-between gap-3">
                        <span>{{ session('success') }}</span>
                        <button type="button" class="-mr-1 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-emerald-700 transition hover:bg-emerald-100 focus:outline-none focus:ring-4 focus:ring-emerald-200" data-dismiss-alert aria-label="Dismiss alert">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('sale.update', $sale) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_20rem]">
                    <div class="space-y-6">
                        <!-- Main Form Card - Unified -->
                        <section class="form-panel reveal rounded-2xl p-6 sm:p-8" style="--reveal-delay: 70ms;">
                            <div class="mb-6">
                                <h3 class="text-xl font-extrabold text-slate-900">Sale Transaction Details</h3>
                                <p class="mt-1 text-sm font-medium text-slate-500">Update the sale transaction details below.</p>
                            </div>

                            <!-- Group 1: When & Who -->
                            <div class="space-y-5">
                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                    <div>
                                        <label for="Date" class="form-label">Sale date <span class="text-rose-500">*</span></label>
                                        <x-text-input id="Date" name="Date" type="date" class="form-field mt-2 block w-full" :value="old('Date', $sale->Date)" required />
                                        <p class="mt-1 text-xs font-medium text-slate-500">When did this sale occur?</p>
                                        <x-input-error class="mt-2" :messages="$errors->get('Date')" />
                                    </div>

                                    <div>
                                        <label for="customer_id" class="form-label">Customer <span class="text-rose-500">*</span></label>
                                        <select name="customer_id" id="customer_id" class="form-field mt-2 block w-full px-4 py-2.5 shadow-sm" required>
                                            <option value="">— Select a customer —</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ old('customer_id', $sale->customer_id) == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-xs font-medium text-slate-500">Who purchased this product?</p>
                                        <x-input-error class="mt-2" :messages="$errors->get('customer_id')" />
                                    </div>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="my-6 h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>

                            <!-- Group 2: What & How Much -->
                            <div class="space-y-5">
                                <div class="quick-tip mb-4 rounded-lg">
                                    <p class="text-xs font-semibold text-emerald-900">💡 Quick tip:</p>
                                    <p class="mt-1 text-xs font-medium text-emerald-800">Select a product first to see its pricing breakdown. The system auto-calculates the amount based on quantity and rate!</p>
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                    <div>
                                        <label for="product_id" class="form-label">Product <span class="text-rose-500">*</span></label>
                                        <select name="product_id" id="product_id" class="form-field mt-2 block w-full px-4 py-2.5 shadow-sm" onchange="getRate(this.value)" required>
                                            <option value="">— Select a product —</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ old('product_id', $sale->product_id) == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }}{{ $product->salesUnit ? ' (' . $product->salesUnit->name . ')' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-xs font-medium text-slate-500">Which product was sold?</p>
                                        <x-input-error class="mt-2" :messages="$errors->get('product_id')" />
                                        <div id="pricing-breakdown" class="hidden mt-3 rounded-lg border border-gray-200 bg-gray-50 p-4">
                                            <div class="grid gap-3 text-sm sm:grid-cols-3">
                                                <div>
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Base price</p>
                                                    <p class="mt-1 font-bold text-gray-900" data-base-rate>Rs. 0.00 / base unit</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Conversion</p>
                                                    <p class="mt-1 font-bold text-gray-900" data-conversion>1 sales unit = 1 base unit</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Sales price</p>
                                                    <p class="mt-1 font-bold text-gray-900" data-sales-rate>Rs. 0.00 / sales unit</p>
                                                </div>
                                            </div>
                                            <p class="mt-3 text-xs text-gray-500" data-formula>Rs. 0.00 × conversion = Rs. 0.00</p>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="quantity" class="form-label">Quantity <span class="text-rose-500">*</span></label>
                                        <x-text-input id="quantity" name="quantity" type="number" step="0.01" min="0.01" class="form-field mt-2 block w-full" :value="old('quantity', $sale->quantity)" placeholder="e.g., 50" onchange="calculateAmount();" oninput="calculateAmount();" required />
                                        <p class="mt-1 text-xs font-medium text-slate-500">How many units were sold?</p>
                                        <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                                    </div>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="my-6 h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>

                            <!-- Group 3: Pricing & Amount -->
                            <div class="space-y-5">
                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                    <div>
                                        <label for="rate" class="form-label">Rate per unit (Rs.) <span class="text-rose-500">*</span></label>
                                        <x-text-input id="rate" name="rate" type="number" step="0.01" min="0" class="form-field mt-2 block w-full" :value="old('rate', $sale->rate)" oninput="calculateAmount();" placeholder="Auto-calculated" required />
                                        <p class="mt-1 text-xs font-medium text-slate-500">Auto-calculated from product's base price. You can override for special pricing.</p>
                                        <x-input-error class="mt-2" :messages="$errors->get('rate')" />
                                    </div>

                                    <div>
                                        <label for="amount" class="form-label">Total Amount (Rs.) <span class="text-rose-500">*</span></label>
                                        <x-text-input id="amount" name="amount" type="number" step="0.01" min="0" class="form-field mt-2 block w-full" :value="old('amount', $sale->amount)" placeholder="Auto-calculated" required />
                                        <p class="mt-1 text-xs font-medium text-slate-500">Calculated as quantity × rate. Override if final billed amount differs.</p>
                                        <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Save Buttons -->
                            <div class="mt-8 flex flex-col gap-3 border-t-2 border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-between">
                                <div class="order-2 sm:order-1">
                                    <a href="{{ route('sale.index') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border-2 border-slate-200 bg-white px-5 py-3 text-sm font-extrabold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 sm:w-auto">
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
                                        Update Sale Entry
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
                        <h3 class="text-lg font-extrabold text-slate-900">Ready to update?</h3>
                        <p class="mt-2 text-sm font-medium leading-6 text-slate-500">Make sure all required fields (marked with <span class="text-rose-500 font-black">*</span>) are filled. Double-check the quantity and pricing before saving.</p>

                        <div class="mt-6 space-y-3">
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-slate-900 to-slate-800 px-5 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-slate-900/20 transition-all hover:from-emerald-700 hover:to-emerald-600 hover:shadow-emerald-500/30 focus:outline-none focus:ring-4 focus:ring-emerald-200">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Update Sale Entry
                            </button>
                            <a href="{{ route('sale.index') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border-2 border-slate-200 bg-white px-5 py-3 text-sm font-extrabold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancel
                            </a>
                        </div>

                        <div class="mt-6 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-xs font-bold text-slate-700">📌 Remember:</p>
                            <ul class="mt-2 space-y-1 text-xs font-medium text-slate-600">
                                <li>• Rate auto-calculates from product pricing</li>
                                <li>• Amount = Quantity × Rate</li>
                                <li>• Stock automatically adjusts on update</li>
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
            });
        </script>
    @endpush
</x-app-layout>
