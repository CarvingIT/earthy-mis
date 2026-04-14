<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('MIS Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('societies.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 block hover:shadow-md transition">
                    <h3 class="text-sm text-gray-500">Societies</h3>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $societiesCount }}</p>
                </a>

                <a href="{{ route('vehicles.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 block hover:shadow-md transition">
                    <h3 class="text-sm text-gray-500">Vehicles</h3>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $vehiclesCount }}</p>
                </a>

                <a href="{{ route('customers.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 block hover:shadow-md transition">
                    <h3 class="text-sm text-gray-500">Customers</h3>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $customersCount }}</p>
                </a>

                <a href="{{ route('products.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 block hover:shadow-md transition">
                    <h3 class="text-sm text-gray-500">Products</h3>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $productsCount }}</p>
                </a>
            </div>

            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Logged in user can now create, edit, and delete societies, vehicles, customers, and products.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
