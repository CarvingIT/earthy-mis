<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Logistics</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('logistics.store') }}" class="space-y-4">
                    @csrf

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
                        <x-input-label for="start_time" value="Start Time" />
                        <x-text-input id="start_time" name="start_time" type="datetime-local" class="mt-1 block w-full" :value="old('start_time')" />
                        <x-input-error class="mt-2" :messages="$errors->get('start_time')" />
                    </div>

                    <div>
                        <x-input-label for="end_time" value="End Time" />
                        <x-text-input id="type" name="end_time" type="datetime-local" class="mt-1 block w-full" :value="old('end_time')" />
                        <x-input-error class="mt-2" :messages="$errors->get('end_time')" />
                    </div>
            
                    <div>
                        <x-input-label for="type" value="Running Kms" />
                        <x-text-input id="running_kms" name="running_kms" type="text" class="mt-1 block w-full" :value="old('running_kms')" />
                        <x-input-error class="mt-2" :messages="$errors->get('running_kms')" />
                    </div>
    

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('vehicles.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
