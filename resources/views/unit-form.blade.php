<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(empty($unit->id))
                New Unit
            @else
                Edit Unit
            @endif
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">
                <form method="POST" action="{{ url('/admin/saveunit') }}">
                    @csrf

                    <!-- Hidden Unit ID field -->
                    <input type="hidden" name="unit_id" value="{{ $unit->id ?? '' }}">

                    <!-- Unit Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Name of the unit <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            placeholder="Name of the unit"
                            value="{{ $unit->name ?? '' }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            required>
                    </div>

                    <!-- Related Unit ID -->
                    <div class="mb-4">
                        <label for="related_unit_id" class="block text-sm font-medium text-gray-700">
                            Related Unit
                        </label>
                        <select
                            name="related_unit_id"
                            id="related_unit_id"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select a Related Unit --</option>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}" @if($unit->related_unit_id == $u->id) selected @endif>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Related Unit Quantity -->
                    <div class="mb-4">
                        <label for="related_unit_quantity" class="block text-sm font-medium text-gray-700">
                            Related Unit Quantity
                        </label>
                        <input
                            type="text"
                            name="related_unit_quantity"
                            id="related_unit_quantity"
                            placeholder="e.g., 10 for 1 Box = 10 KG"
                            value="{{ $unit->related_unit_quantity ?? '' }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Description
                        </label>
                        <textarea
                            name="description"
                            id="description"
                            rows="4"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ $unit->description ?? '' }}</textarea>
                    </div>

                    <!-- Action Buttons -->
                    @if(Auth::user() && Auth::user()->is_admin)
                        <div class="flex justify-end space-x-3">
                            <a href="{{ url('/admin/units') }}" class="px-4 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                Save
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
