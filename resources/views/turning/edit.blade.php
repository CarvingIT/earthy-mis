<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Turning</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('turning.update', $turning) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="windrow_id" value="Windrow Number" />
                        <select name="windrow_id" class="mt-1 block w-full">
                            <option value="">Select Windrow Number</option>
                            @foreach($windrow as $win)
                                <option value="{{ $win->id }}" @if($win->id == $turning->windrow_id) selected @endif>{{ $win->windrow_number }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('windrow_id')" />

                    </div>

                    <div>
                        <x-input-label for="Date" value="Date" />
                        <x-text-input id="Date" name="Date" type="date" class="mt-1 block w-full" :value="old('Date`', $turning->Date)" />
                        <x-input-error class="mt-2" :messages="$errors->get('Date')" />
                    </div>

                    <div>
                        <x-input-label for="duration" value="Duration" />
                        <x-text-input id="duration" name="duration" type="text" class="mt-1 block w-full" :value="old('duration', $turning->duration)" />
                        <x-input-error class="mt-2" :messages="$errors->get('duration')" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('turning.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
                        <x-primary-button>Update</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
