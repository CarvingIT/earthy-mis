<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Sale</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('sale.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="Date" value="Date" />
                        <x-text-input id="Date" name="Date" type="date" class="mt-1 block w-full" :value="old('Date')" />
                        <x-input-error class="mt-2" :messages="$errors->get('Date')" />
                    </div>

                    <div>
                        <x-input-label for="product_id" value="Product" />
                        <select name="product_id" class="mt-1 block w-full">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('product_id')" />
                    </div>

                    <div>
                        <x-input-label for="quantity" value="Quantity" />
                        <x-text-input id="quantity" name="quantity" type="number" class="mt-1 block w-full" :value="old('quantity')" />
                        <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                    </div>
            
                    <div>
                        <x-input-label for="customer_id" value="Customer" />
                        <select name="customer_id" class="mt-1 block w-full">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('customer_id')" />
                    </div>

                    <div>
                        <x-input-label for="rate" value="Rate" />
                        <x-text-input id="rate" name="rate" type="text" class="mt-1 block w-full" :value="old('rate')" />
                        <x-input-error class="mt-2" :messages="$errors->get('rate')" />
                    </div>
    
                    <div>
                        <x-input-label for="amount" value="Amount" />
                        <x-text-input id="amount" name="amount" type="text" class="mt-1 block w-full" :value="old('amount')" />
                        <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                    </div>
    

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('sale.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
