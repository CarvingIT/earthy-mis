<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Products</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-end">
                <a href="{{ route('products.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Add Product</a>
            </div>

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($products as $product)
                            <tr>
                                <td class="px-4 py-2">{{ $product->name }}</td>
                                <td class="px-4 py-2">{{ $product->sku }}</td>
                                <td class="px-4 py-2">{{ number_format($product->price, 2) }}</td>
                                <td class="px-4 py-2">{{ $product->stock }}</td>
                                <td class="px-4 py-2 text-right space-x-3">
                                    <a class="text-indigo-600" href="{{ route('products.edit', $product) }}">Edit</a>
                                    <form class="inline" method="POST" action="{{ route('products.destroy', $product) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600" onclick="return confirm('Delete this product?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-gray-500">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $products->links() }}</div>
        </div>
    </div>
</x-app-layout>
