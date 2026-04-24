<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Windrow</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('windrow.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="windrow_number" value="Windrow Number" />
                        <x-text-input id="windrow_number" name="windrow_number" type="text" class="mt-1 block w-full" :value="old('windrow_number')" />
                        <x-input-error class="mt-2" :messages="$errors->get('windrow_number')" />
                    </div>
    
                    <div>
                        <x-input-label for="start_date" value="Start Date" />
                        <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date')" />
                        <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                    </div>

                    <div>
                        <x-input-label for="end_date" value="End Date" />
                        <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="old('end_date')" />
                        <x-input-error class="mt-2" :messages="$errors->get('end_date')" />
                    </div>
            
                    <div>
                        <x-input-label for="weight_in" value="Weight IN" />
                        <x-text-input id="weight_in" name="weight_in" type="text" class="mt-1 block w-full" :value="old('weight_in')" />
                        <x-input-error class="mt-2" :messages="$errors->get('weight_in')" />
                    </div>

                    <div>
                        <x-input-label for="out_date" value="Out Date" />
                        <x-text-input id="out_date" name="out_date" type="date" class="mt-1 block w-full" :value="old('out_date')" />
                        <x-input-error class="mt-2" :messages="$errors->get('out_date')" />
                    </div>
            
                    <div>
                        <x-input-label for="screening_date" value="Screening Date" />
                        <x-text-input id="screening_date" name="screening_date" type="date" class="mt-1 block w-full" :value="old('screening_date')" />
                        <x-input-error class="mt-2" :messages="$errors->get('screening_date')" />
                    </div>
            
    

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('logistics.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
