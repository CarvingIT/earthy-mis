<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Units</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Add New Unit Button -->
            <div class="mb-4 flex justify-between items-center">
                @if(Auth::user() && Auth::user()->is_admin)
                    <a href="{{ url('/admin/unit-form/new') }}" class="px-4 py-2 text-white rounded-md" style="background-color:#16a34a;border:1px solid #16a34a;" onmouseover="this.style.backgroundColor='#15803d';this.style.borderColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a';this.style.borderColor='#16a34a'">Add New Unit</a>
                @endif
                <input type="text" id="mySearchInput" placeholder="Search units..." style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 300px;">
            </div>

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

            <!-- Units Table -->
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-4" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table id="units" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Related Unit</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Related Unit Quantity</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($units as $unit)
                            <tr>
                                <td class="px-4 py-2">{{ $unit->name }}</td>
                                <td class="px-4 py-2">
                                    @if($unit->unit)
                                        {{ $unit->unit->name }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    @if($unit->related_unit_quantity)
                                        {{ $unit->related_unit_quantity }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    @if($unit->description)
                                        {{ Str::limit($unit->description, 50, '...') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center space-x-2">
                                    <a href="{{ url('/admin/unit/' . $unit->id) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(Auth::user() && Auth::user()->is_admin)
                                        <a href="{{ url('/admin/unit-form/' . $unit->id) }}" class="text-green-600 hover:text-green-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="text-red-600 hover:text-red-900 delete_unit" data-unit-id="{{ $unit->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-gray-500">No units found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Dialog -->
    <div id="delete_dialog" title="Are you sure?">
        <p>Are you sure you want to delete this unit?</p>
    </div>

    <!-- Hidden form for delete action -->
    <form id="delete_form" method="POST" action="{{ url('/admin/unit/delete') }}" style="display:none;">
        @csrf
        <input type="hidden" id="delete_unit_id" name="unit_id">
    </form>

    @push('js')
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="{{ asset('js/Unit.js') }}"></script>
    @endpush
</x-app-layout>
