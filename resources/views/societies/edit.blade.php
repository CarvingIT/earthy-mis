<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Society</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('societies.update', $society) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Name" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $society->name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="address" value="Address" />
                        <textarea id="address" name="address" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('address', $society->address) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('address')" />
                    </div>

                    <div>
                        <x-input-label for="city" value="City" />
                        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $society->city)" />
                        <x-input-error class="mt-2" :messages="$errors->get('city')" />
                    </div>

                    <div>
                        <x-input-label for="contact_person" value="Contact Person" />
                        <x-text-input id="contact_person" name="contact_person" type="text" class="mt-1 block w-full" :value="old('contact_person', $society->contact_person)" />
                        <x-input-error class="mt-2" :messages="$errors->get('contact_person')" />
                    </div>

                    <div>
                        <x-input-label for="phone" value="Phone" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $society->phone)" />
                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('societies.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
                        <x-primary-button>Update</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
