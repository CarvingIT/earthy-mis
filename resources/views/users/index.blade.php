<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Users (Staff)</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-end">
                <a href="{{ route('users.create') }}" class="px-4 py-2 text-white rounded-md" style="background-color:#16a34a;border:1px solid #16a34a;" onmouseover="this.style.backgroundColor='#15803d';this.style.borderColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a';this.style.borderColor='#16a34a'">Add User</a>
            </div>

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-4">
                <table data-datatable class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($users as $user)
                            <tr>
                                <td class="px-4 py-2">{{ $user->name }}</td>
                                <td class="px-4 py-2">{{ $user->email }}</td>
                                <td class="px-4 py-2">
                                    @if ($user->is_admin)
                                        <span class="inline-flex items-center rounded-md bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700">Admin</span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">Staff</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ $user->created_at?->format('d M Y') }}</td>
                                <td class="px-4 py-2 text-right space-x-3">
                                    <a class="text-green-600" href="{{ route('users.edit', $user) }}">Edit</a>
                                    @if (auth()->id() !== $user->id)
                                        <form class="inline" method="POST" action="{{ route('users.destroy', $user) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600" onclick="return confirm('Delete this user?')">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-gray-500">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
