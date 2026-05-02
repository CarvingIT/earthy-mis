<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Unit Details</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Messages -->
            @if (session('alert-success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('alert-success') }}</div>
            @endif
            @if (session('alert-danger'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('alert-danger') }}</div>
            @endif
            @if (session('alert-warning'))
                <div class="mb-4 p-3 bg-yellow-100 text-yellow-700 rounded">{{ session('alert-warning') }}</div>
            @endif
            @if (session('alert-info'))
                <div class="mb-4 p-3 bg-blue-100 text-blue-700 rounded">{{ session('alert-info') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">
                <!-- Edit Button -->
                @if(Auth::user() && Auth::user()->is_admin && $unit)
                    <div class="mb-4 flex justify-end">
                        <a href="{{ url('/admin/unit-form/' . $unit->id) }}" class="px-4 py-2 text-white rounded-md" style="background-color:#16a34a;border:1px solid #16a34a;" onmouseover="this.style.backgroundColor='#15803d';this.style.borderColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a';this.style.borderColor='#16a34a'">
                            Edit Unit
                        </a>
                    </div>
                @endif

                @if($unit)
                    <!-- Name of the unit -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Name of the unit</label>
                        <input
                            type="text"
                            value="{{ $unit->name }}"
                            disabled
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-700">
                    </div>

                    <!-- Related Unit -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Related Unit</label>
                        <input
                            type="text"
                            value="{{ $unit->unit ? $unit->unit->name : '-' }}"
                            disabled
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-700">
                    </div>

                    <!-- Unit Quantity -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Unit Quantity</label>
                        <input
                            type="text"
                            value="{{ $unit->related_unit_quantity ? $unit->related_unit_quantity : '-' }}"
                            disabled
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-700">
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea
                            disabled
                            rows="4"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-700">{{ $unit->description ? $unit->description : '-' }}</textarea>
                    </div>

                    <!-- Back Button -->
                    <div class="flex justify-end">
                        <a href="{{ url('/admin/units') }}" class="px-4 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                            Back to Units
                        </a>
                    </div>
                @else
                    <div class="text-center text-gray-500">
                        <p>Unit not found.</p>
                        <a href="{{ url('/admin/units') }}" class="text-indigo-600 hover:text-indigo-900">Go back to units list</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
