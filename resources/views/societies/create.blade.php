@push('js')
<script src="/build/assets/jquery-ui.js"></script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Society</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('societies.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Name" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="address" value="Address" />
                        <textarea id="address" name="address" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('address') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('address')" />
                    </div>

                    <div>
                        <x-input-label for="city" value="City" />
                        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city')" />
                        <x-input-error class="mt-2" :messages="$errors->get('city')" />
                    </div>

                    <div>
                        <x-input-label for="joining_month" value="Joining Month" />
                        <x-text-input id="joining_month" name="joining_month" type="text" class="mt-1 block w-full" :value="old('joining_month')" placeholder="March"/>
                        <x-input-error class="mt-2" :messages="$errors->get('joining_month')" />
                    </div>

                    <div>
                        <x-input-label for="flats_families" value="Falts/Families" />
                        <x-text-input id="flats_families" name="flats_families" type="text" class="mt-1 block w-full" :value="old('flats_families')" />
                        <x-input-error class="mt-2" :messages="$errors->get('flats_families')" />
                    </div>

                    <div>
                        <x-input-label for="chairman_name" value="Chairman Name" />
                        <x-text-input id="chairman_name" name="chairman_name" type="text" class="mt-1 block w-full" :value="old('flats_families')" />
                        <x-input-error class="mt-2" :messages="$errors->get('chairman_name')" />
                    </div>

                    <div>
                        <x-input-label for="secretary_name" value="Secretary Name" />
                        <x-text-input id="secretary_name" name="secretary_name" type="text" class="mt-1 block w-full" :value="old('secretary_name')" />
                        <x-input-error class="mt-2" :messages="$errors->get('secretary_name')" />
                    </div>

                    <div>
                        <x-input-label for="contact_person_email" value="Contact Person Email" />
                        <x-text-input id="contact_person" name="contact_person_email" type="text" class="mt-1 block w-full" :value="old('contact_person_email')" />
                        <x-input-error class="mt-2" :messages="$errors->get('contact_person_email')" />
                    </div>

                    <div>
                        <x-input-label for="phone" value="Contact Number" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" />
                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('societies.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
