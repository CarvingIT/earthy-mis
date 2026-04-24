<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Fuel</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('fuel.update', $fuel) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="date" value="Date" />
                        <x-text-input id="Date" name="Date" type="date" class="mt-1 block w-full" :value="old('Date', $fuel->Date)" />
                        <x-input-error class="mt-2" :messages="$errors->get('Date')" />
                    </div>

                    <div>
                        <x-input-label for="quantity" value="Quantity" />
                        <x-text-input id="quantity" name="quantity" type="text" class="mt-1 block w-full" :value="old('quantity', $fuel->quantity)" />
                        <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                    </div>

                    <div>
                        <x-input-label for="type" value="Type" />
                        <x-text-input id="type" name="type" type="text" class="mt-1 block w-full" :value="old('type', $fuel->type)" />
                        <x-input-error class="mt-2" :messages="$errors->get('type')" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('fuel.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
                        <x-primary-button>Update</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
