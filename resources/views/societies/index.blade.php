<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Societies</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-end">
                <a href="{{ route('societies.create') }}" class="px-4 py-2 text-white rounded-md" style="background-color:#16a34a;border:1px solid #16a34a;" onmouseover="this.style.backgroundColor='#15803d';this.style.borderColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a';this.style.borderColor='#16a34a'">Add Society</a>
            </div>

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-4" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table id="societies-table" data-datatable class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">City</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Joining Month</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Flats/Families</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Chairman/Contact</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Secretary/Email</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($societies as $society)
                            <tr>
                                <td class="px-4 py-2">{{ $society->name }}</td>
                                <td class="px-4 py-2">{{ $society->city }}</td>
                                <td class="px-4 py-2">{{ $society->joining_month }}</td>
                                <td class="px-4 py-2">{{ $society->flats_families }}</td>
                                <td class="px-4 py-2">{{ $society->chairman_name }}</td>
                                <td class="px-4 py-2">{{ $society->secretary_name }}<br><small class="text-gray-600">{{ $society->contact_person_email }}</small></td>
                                <td class="px-4 py-2">{{ $society->phone }}</td>
                                <td class="px-4 py-2 text-right space-x-3">
                                    <a class="text-green-600" href="{{ route('societies.edit', $society) }}">Edit</a>
                                    <form class="inline" method="POST" action="{{ route('societies.destroy', $society) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600" onclick="return confirm('Delete this society?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-4 text-center text-gray-500">No societies found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
