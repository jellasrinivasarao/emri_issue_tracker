<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Working Hours</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                {{-- Create form --}}
                <div class="col-span-1">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">Add Working Calendar</h3>
                        <form method="POST" action="{{ route('working.calendars.store') }}" class="mt-4 space-y-3">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Code</label>
                                <input name="calendar_code" value="{{ old('calendar_code') }}" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                                @error('calendar_code') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Name</label>
                                <input name="calendar_name" value="{{ old('calendar_name') }}" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                                @error('calendar_name') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Timezone</label>
                                <input name="timezone" value="{{ old('timezone', config('app.timezone')) }}" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                                @error('timezone') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                            </div>
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="is_active" checked class="h-4 w-4" />
                                    Active
                                </label>
                                <button class="ml-auto inline-flex items-center rounded-md bg-slate-900 px-3 py-2 text-sm font-semibold text-white">Create</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- List --}}
                <div class="col-span-1 lg:col-span-2">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">Working Calendars</h3>

                        <div class="mt-4 overflow-x-auto">
                            <table class="w-full table-auto text-sm">
                                <thead class="bg-slate-50">
                                    <tr class="text-left">
                                        <th class="px-3 py-2">Code</th>
                                        <th class="px-3 py-2">Name</th>
                                        <th class="px-3 py-2">Timezone</th>
                                        <th class="px-3 py-2">Active</th>
                                        <th class="px-3 py-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($calendars as $cal)
                                    <tr>
                                        <td class="px-3 py-2">{{ $cal->calendar_code }}</td>
                                        <td class="px-3 py-2">{{ $cal->calendar_name }}</td>
                                        <td class="px-3 py-2">{{ $cal->timezone }}</td>
                                        <td class="px-3 py-2">{{ $cal->is_active ? 'Yes' : 'No' }}</td>
                                        <td class="px-3 py-2">
                                            <details class="inline-block">
                                                <summary class="cursor-pointer text-blue-600">Edit</summary>
                                                <form method="POST" action="{{ route('working.calendars.update', $cal->calendar_id) }}" class="mt-2 space-y-2 p-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <input name="calendar_code" value="{{ old('calendar_code', $cal->calendar_code) }}" class="rounded-md border border-slate-300 px-2 py-1 text-sm" />
                                                        <input name="calendar_name" value="{{ old('calendar_name', $cal->calendar_name) }}" class="rounded-md border border-slate-300 px-2 py-1 text-sm" />
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <input name="timezone" value="{{ old('timezone', $cal->timezone) }}" class="rounded-md border border-slate-300 px-2 py-1 text-sm" />
                                                        <label class="flex items-center gap-2">
                                                            <input type="checkbox" name="is_active" value="1" {{ $cal->is_active ? 'checked' : '' }} class="h-4 w-4" /> Active
                                                        </label>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <button class="inline-flex items-center rounded-md bg-amber-600 px-3 py-1 text-xs font-semibold text-white">Save</button>
                                                        <form method="POST" action="{{ route('working.calendars.destroy', $cal->calendar_id) }}" onsubmit="return confirm('Delete calendar?');">@csrf @method('DELETE') <button class="inline-flex items-center rounded-md bg-red-600 px-3 py-1 text-xs font-semibold text-white">Delete</button></form>
                                                    </div>
                                                </form>
                                            </details>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500">No working calendars configured.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
