<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <div>

                <h2 class="text-xl font-semibold text-slate-800">
                    Create SLA Configuration
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Create a new SLA master configuration.
                </p>

            </div>

            <a href="{{ route(
                    'admin.sla-configurations.index'
                ) }}" class="inline-flex items-center gap-2
                       rounded-xl border border-slate-300
                       bg-white px-4 py-2.5 text-sm
                       font-medium text-slate-700
                       hover:bg-slate-50">

                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>

                Back to SLA List

            </a>

        </div>

    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            {{-- Validation Summary --}}

            @if($errors->any())

            <div class="mb-6 rounded-xl border
                           border-red-200 bg-red-50 p-4">

                <div class="mb-2 text-sm font-semibold
                               text-red-700">
                    Please correct the following errors:
                </div>

                <ul class="list-disc space-y-1 pl-5
                               text-sm text-red-600">

                    @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                    @endforeach

                </ul>

            </div>

            @endif


            <form method="POST" action="{{ route(
                    'admin.sla-configurations.store'
                ) }}">

                @csrf

                @include('admin.sla-configurations._form',['sla' => null,'calendars' => $calendars])

            </form>

        </div>

    </div>

</x-app-layout>