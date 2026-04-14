<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit User</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Name" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div>
                        <x-input-label for="password" value="Password (Leave blank to keep current)" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
                        <x-input-error class="mt-2" :messages="$errors->get('password')" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" value="Confirm Password" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
                    </div>

                    <label for="is_admin" class="inline-flex items-center">
                        <input id="is_admin" name="is_admin" type="checkbox" value="1" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500" @checked(old('is_admin', $user->is_admin))>
                        <span class="ms-2 text-sm text-gray-700">Admin access</span>
                    </label>
                    <x-input-error class="mt-1" :messages="$errors->get('is_admin')" />

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('users.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
                        <x-primary-button>Update</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
