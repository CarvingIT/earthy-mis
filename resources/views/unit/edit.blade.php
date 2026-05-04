<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Unit</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('units.update', $unit) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Name<span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $unit->name) }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $unit->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="related_unit_id" class="block text-sm font-medium text-gray-700">Related Unit</label>
                        <select id="related_unit_id" name="related_unit_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select a unit --</option>
                            @foreach ($relatedUnits as $relatedUnit)
                                <option value="{{ $relatedUnit->id }}" {{ old('related_unit_id', $unit->related_unit_id) == $relatedUnit->id ? 'selected' : '' }}>{{ $relatedUnit->name }}</option>
                            @endforeach
                        </select>
                        @error('related_unit_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="related_unit_quantity" class="block text-sm font-medium text-gray-700">Related Unit Quantity</label>
                        <input type="number" id="related_unit_quantity" name="related_unit_quantity" value="{{ old('related_unit_quantity', $unit->related_unit_quantity) }}" step="0.01" min="0" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        @error('related_unit_quantity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 text-white rounded-md" style="background-color:#16a34a;border:1px solid #16a34a;" onmouseover="this.style.backgroundColor='#15803d';this.style.borderColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a';this.style.borderColor='#16a34a'">Update</button>
                        <a href="{{ route('units.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
