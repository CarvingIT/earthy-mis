<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Vehicle</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('vehicles.update', $vehicle) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="registration_number" value="Registration Number" />
                        <x-text-input id="registration_number" name="registration_number" type="text" class="mt-1 block w-full" :value="old('registration_number', $vehicle->registration_number)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('registration_number')" />
                    </div>

                    <div>
                        <x-input-label for="type" value="Type" />
                        <x-text-input id="type" name="type" type="text" class="mt-1 block w-full" :value="old('type', $vehicle->type)" />
                        <x-input-error class="mt-2" :messages="$errors->get('type')" />
                    </div>

                    <div>
                        <x-input-label for="brand" value="Brand" />
                        <x-text-input id="brand" name="brand" type="text" class="mt-1 block w-full" :value="old('brand', $vehicle->brand)" />
                        <x-input-error class="mt-2" :messages="$errors->get('brand')" />
                    </div>

                    <div>
                        <x-input-label for="model" value="Model" />
                        <x-text-input id="model" name="model" type="text" class="mt-1 block w-full" :value="old('model', $vehicle->model)" />
                        <x-input-error class="mt-2" :messages="$errors->get('model')" />
                    </div>

                    <div>
                        <x-input-label for="color" value="Color" />
                        <x-text-input id="color" name="color" type="text" class="mt-1 block w-full" :value="old('color', $vehicle->color)" />
                        <x-input-error class="mt-2" :messages="$errors->get('color')" />
                    </div>

                    <div>
                        <x-input-label for="purchased_on" value="Purchased On" />
                        <x-text-input id="purchased_on" name="purchased_on" type="date" class="mt-1 block w-full" :value="old('purchased_on', $vehicle->purchased_on)" />
                        <x-input-error class="mt-2" :messages="$errors->get('purchased_on')" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('vehicles.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
                        <x-primary-button>Update</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
