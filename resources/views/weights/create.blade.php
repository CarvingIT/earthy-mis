<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Weights</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('weights.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="date" value="Date" />
                        <x-text-input id="date" name="Date" type="date" class="mt-1 block w-full" :value="old('Date')" />
                        <x-input-error class="mt-2" :messages="$errors->get('Date')" />
                    </div>

                    <div>
                        <x-input-label for="vehicle_id" value="Vehicle" />
                        <select name="vehicle_id" class="mt-1 block w-full">
                            <option value="">Select Vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->registration_number }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('vehicle_id')" />
                    </div>

                    <div>
                        <x-input-label for="gross_weight" value="Gross Weight in kgs" />
                        <x-text-input id="gross_weight" name="gross_weight" type="text" class="mt-1 block w-full" :value="old('gross_weight')" />
                        <x-input-error class="mt-2" :messages="$errors->get('gross_weight')" />
                    </div>
    
                    <div>
                        <x-input-label for="tare_weight" value="Tare Weight in kgs" />
                        <x-text-input id="tare_weight" name="tare_weight" type="text" class="mt-1 block w-full" :value="old('tare_weight')" />
                        <x-input-error class="mt-2" :messages="$errors->get('tare_weight')" />
                    </div>

                    <div>
                        <x-input-label for="net_weight" value="Net Weight in kgs" />
                        <x-text-input id="net_weight" name="net_weight" type="text" class="mt-1 block w-full" :value="old('net_weight')" />
                        <x-input-error class="mt-2" :messages="$errors->get('net_weight')" />
                    </div>

                    <div>
                        <x-input-label for="number_of_buckets" value="Number of Buckets" />
                        <x-text-input id="number_of_buckets" name="number_of_buckets" type="text" class="mt-1 block w-full" :value="old('number_of_buckets')" />
                        <x-input-error class="mt-2" :messages="$errors->get('number_of_buckets')" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('weights.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
