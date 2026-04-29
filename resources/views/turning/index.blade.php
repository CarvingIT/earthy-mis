<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Turnings</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-end">
                <a href="{{ route('turning.create') }}" class="px-4 py-2 text-white rounded-md" style="background-color:#16a34a;border:1px solid #16a34a;" onmouseover="this.style.backgroundColor='#15803d';this.style.borderColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a';this.style.borderColor='#16a34a'">Add Turning</a>
            </div>

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-4" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table id="turning-table" data-datatable class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Windrow Number</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($turnings as $turning)
                            <tr>
                                <td class="px-4 py-2">{{ @$turning->windrow->windrow_number }}</td>
                                <td class="px-4 py-2">{{ $turning->Date }}</td>
                                <td class="px-4 py-2">{{ $turning->duration }}</td>
                                <td class="px-4 py-2 text-right space-x-3">
                                    <a class="text-green-600" href="{{ route('turning.edit', $turning) }}">Edit</a>
                                    <form class="inline" method="POST" action="{{ route('turning.destroy', $turning) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600" onclick="return confirm('Delete this Turning?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-gray-500">No logistic found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
