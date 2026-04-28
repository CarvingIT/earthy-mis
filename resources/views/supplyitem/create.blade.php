<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Supply Item</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('supplyitems.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="date" value="Date" />
                        <x-text-input id="Date" name="Date" type="date" class="mt-1 block w-full" :value="old('Date')" />
                        <x-input-error class="mt-2" :messages="$errors->get('Date')" />
                    </div>

                    <div>
                        <x-input-label for="quantity" value="Quantity" />
                        <x-text-input id="quantity" name="quantity" type="text" class="mt-1 block w-full" :value="old('quantity')" />
                        <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                    </div>
    
                    <div>
                        <x-input-label for="type" value="Item" />
                        <select name="consumable_id" class="mt-1 block w-full">
                            <option value="">Select Consumable Item</option>
                            @foreach($consumables as $item)
                                <option value="{{ $item->id }}">{{ $item->item }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('consumable_id')" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

    

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('supplyitems.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
