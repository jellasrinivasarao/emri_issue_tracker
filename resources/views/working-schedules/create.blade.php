<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Create Working Schedule
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                    <h3 class="text-lg font-semibold text-slate-900">
                        Create Working Schedule
                    </h3>
                </div>

                <form action="{{ route('working-schedules.store') }}" method="POST" class="p-6">
                    @csrf

                    @include('working-schedules._form')
                </form>

            </div>

        </div>
    </div>

</x-app-layout>