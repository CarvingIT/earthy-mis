<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Stock</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('stock.update', $stock) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="Date" value="Date" />
                        <x-text-input id="Date" name="Date" type="date" class="mt-1 block w-full" :value="old('Date', $stock->Date)" />
                        <x-input-error class="mt-2" :messages="$errors->get('Date')" />
                    </div>

                    <div>
                        <x-input-label for="product_id" value="Products" />
                        <select name="product_id" class="mt-1 block w-full">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" @if($product->id == $stock->product_id) selected @endif>{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('product_id')" />

                    </div>

                    <div>
                        <x-input-label for="quantity" value="Current Quantity" />
                        <x-text-input id="quantity" name="quantity" type="text" class="mt-1 block w-full" :value="old('quantity', $stock->quantity)" />
                        <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('stock.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
                        <x-primary-button>Update</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
