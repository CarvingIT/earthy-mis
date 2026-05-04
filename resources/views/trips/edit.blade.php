<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Trip</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('trips.update', $trip) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="date" value="Date" />
                        <x-text-input id="date" name="Date" type="date" class="mt-1 block w-full" :value="old('Date', $trip->Date)" />
                        <x-input-error class="mt-2" :messages="$errors->get('Date')" />
                    </div>

                    <div>
                        <x-input-label for="vehicle_id" value="Vehicle" />
                        <select name="vehicle_id" class="mt-1 block w-full">
                            <option value="">Select Vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" @if($trip->vehicle_id == $vehicle->id) selected @endif>{{ $vehicle->registration_number }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('vehicle_id')" />
                    </div>

                    <div>
                        <x-input-label for="purpose" value="Purpose" />
                        <x-text-input id="purpose" name="purpose" type="text" class="mt-1 block w-full" :value="old('purpose', $trip->purpose)" />
                        <x-input-error class="mt-2" :messages="$errors->get('purpose')" />
                    </div>

                    <div>
                        <x-input-label for="km" value="Kilometers" />
                        <x-text-input id="km" name="km" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('km', $trip->km)" />
                        <x-input-error class="mt-2" :messages="$errors->get('km')" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('trips.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
                        <x-primary-button>Update</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
