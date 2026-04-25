<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Stock</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-end">
                <a href="{{ route('stock.create') }}" class="px-4 py-2 text-white rounded-md" style="background-color:#16a34a;border:1px solid #16a34a;" onmouseover="this.style.backgroundColor='#15803d';this.style.borderColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a';this.style.borderColor='#16a34a'">Add Stock</a>
            </div>

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-4">
                <table id="turning-table" data-datatable class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Latest Quantity</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Created at</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Updated at</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($stocks as $stock)
                            <tr>
                                <td class="px-4 py-2">{{ $stock->Date }}</td>
                                <td class="px-4 py-2">{{ @$stock->product->name }}</td>
                                <td class="px-4 py-2">{{ $stock->quantity }}</td>
                                <td class="px-4 py-2">{{ $sum[$stock->product_id] }}</td>
                                <td class="px-4 py-2">{{ $stock->created_at }}</td>
                                <td class="px-4 py-2">{{ $stock->updated_at }}</td>
                                <td class="px-4 py-2 text-right space-x-3">
                                    <a class="text-green-600" href="{{ route('stock.edit', $stock) }}">Edit</a>
                                    <form class="inline" method="POST" action="{{ route('stock.destroy', $stock) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600" onclick="return confirm('Delete this Stock?')">Delete</button>
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
