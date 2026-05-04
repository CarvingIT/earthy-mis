<script>
    function calculateAmount(){
        const quantity = document.getElementById('quantity').value;
        const rate = document.getElementById('rate').value;
        const amount = quantity * rate;
        document.getElementById('amount').value = amount;
    }

    function getRate(productId){
        if (!productId) {
            document.getElementById('rate').value = '';
            document.getElementById('amount').value = '';
            return;
        }

        fetch('/get_product_rate/ajax/' + productId)
            .then(response => response.json())
            .then(data => {
                if (data.rate !== undefined) {
                    document.getElementById('rate').value = data.rate;
                    calculateAmount();
                }
            })
            .catch(error => console.error('Error:', error));
    }
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
                                <option value="{{ $product->id }}" @if($sale->product_id == $product->id) selected @endif>{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('product_id')" />
                    </div>

                    <div>
                        <x-input-label for="quantity" value="Quantity" />
                        <x-text-input id="quantity" name="quantity" type="number" class="mt-1 block w-full" :value="old('quantity', $sale->quantity)" onchange="calculateAmount();" oninput="calculateAmount();" />
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
                        <x-input-label for="rate" value="Rate" />
                        <x-text-input id="rate" name="rate" type="number" step="0.01" class="mt-1 block w-full" :value="old('rate', $sale->rate)" readonly />
                        <x-input-error class="mt-2" :messages="$errors->get('rate')" />
                    </div>

                    <div>
                        <x-input-label for="amount" value="Amount" />
                        <x-text-input id="amount" name="amount" type="number" step="0.01" class="mt-1 block w-full" :value="old('amount', $sale->amount)" readonly />
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
