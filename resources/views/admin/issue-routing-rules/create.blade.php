<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <div>

                <h2 class="text-xl font-semibold text-slate-800">
                    Create Issue Routing Rule
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Configure issue routing based on project and support criteria.
                </p>

            </div>

            <a href="{{ route('admin.issue-routing-rules.index') }}"
                class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Back
            </a>

        </div>

    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if(session('success'))

            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>

            @endif


            @if(session('error'))

            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>

            @endif


            @include('admin.issue-routing-rules._form')

        </div>

    </div>

</x-app-layout>