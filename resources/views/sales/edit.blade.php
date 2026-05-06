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

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Sale</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('sale.update', $sale) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="Date" value="Date" />
                        <x-text-input id="Date" name="Date" type="date" class="mt-1 block w-full" :value="old('Date', $sale->Date)" />
                        <x-input-error class="mt-2" :messages="$errors->get('Date')" />
                    </div>

                    <div>
                        <x-input-label for="product_id" value="Products" />
                        <select id="product_id" name="product_id" class="mt-1 block w-full" onchange="getRate(this.value);">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" @if($sale->product_id == $product->id) selected @endif>
                                    {{ $product->name }}{{ $product->salesUnit ? ' (' . $product->salesUnit->name . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
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
                        <x-input-label for="quantity" value="Quantity" />
                        <x-text-input id="quantity" name="quantity" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('quantity', $sale->quantity)" onchange="calculateAmount();" oninput="calculateAmount();" />
                        <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                    </div>

                    <div>
                        <x-input-label for="customer_id" value="Customers" />
                        <select name="customer_id" class="mt-1 block w-full">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" @if($sale->customer_id == $customer->id) selected @endif>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('customer_id')" />
                    </div>

                    <div>
                        <x-input-label for="rate" value="Derived sales-unit rate (editable)" />
                        <x-text-input id="rate" name="rate" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('rate', $sale->rate)" oninput="calculateAmount();" />
                        <p class="mt-2 text-xs text-gray-500">Auto-calculated from the product’s base price and sales-unit conversion. You can override it for special pricing.</p>
                        <x-input-error class="mt-2" :messages="$errors->get('rate')" />
                    </div>

                    <div>
                        <x-input-label for="amount" value="Amount" />
                        <x-text-input id="amount" name="amount" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('amount', $sale->amount)" />
                        <p class="mt-2 text-xs text-gray-500">Manual override is allowed if the final billed amount differs from the calculation.</p>
                        <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('sale.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
                        <x-primary-button>Update</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
