<x-app-layout>

    <x-slot name="header">

        <h2 class="text-xl font-semibold text-gray-800">
            Issue Management
        </h2>

    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-7xl px-4">

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">

                    <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">

                        <div>

                            <p class="text-sm text-slate-600">
                                Manage issues, routing, assignments and lifecycle.
                            </p>

                        </div>

                        <div>

                            <input id="issue-search" type="text" placeholder="Search"
                                class="rounded-2xl border border-slate-200 px-4 py-2 text-sm outline-none">

                        </div>

                        <a href="{{ route('issues.create') }}"
                            class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            Create Issue
                        </a>

                    </div>

                </div>


                @if(session('success'))

                <div class="px-5 py-4">

                    <div class="rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">

                        {{ session('success') }}

                    </div>

                </div>

                @endif


                <div class="overflow-x-auto">

                    <div class="max-h-[550px] overflow-auto">

                        <table class="min-w-full divide-y divide-slate-200">

                            <thead class="sticky top-0 z-10 bg-purple-100">

                                <tr>

                                    <th class="px-5 py-3 text-left text-xs uppercase text-purple-900">
                                        Issue
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs uppercase text-purple-900">
                                        Subject
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs uppercase text-purple-900">
                                        Priority
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs uppercase text-purple-900">
                                        Team
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs uppercase text-purple-900">
                                        Status
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs uppercase text-purple-900">
                                        Action
                                    </th>

                                </tr>

                            </thead>


                            <tbody class="divide-y divide-slate-200">

                                @forelse($issues as $issue)

                                <tr>

                                    <td class="px-5 py-3">

                                        <div class="font-semibold text-slate-900">
                                            {{ $issue->issue_number }}
                                        </div>

                                        <div class="text-xs text-slate-500">
                                            {{ $issue->issue_category ?? '-' }}
                                        </div>

                                    </td>


                                    <td class="px-5 py-3 text-sm">
                                        {{ $issue->subject }}
                                    </td>


                                    <td class="px-5 py-3">

                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold
                                                @switch($issue->priority)
                                                    @case('CRITICAL')
                                                        bg-rose-100 text-rose-700
                                                        @break
                                                    @case('HIGH')
                                                        bg-orange-100 text-orange-700
                                                        @break
                                                    @case('MEDIUM')
                                                        bg-blue-100 text-blue-700
                                                        @break
                                                    @default
                                                        bg-slate-100 text-slate-700
                                                @endswitch
                                            ">
                                            {{ $issue->priority }}
                                        </span>

                                    </td>


                                    <td class="px-5 py-3 text-sm">

                                        {{ $issue->team?->team_name ?? 'Not Assigned' }}

                                    </td>


                                    <td class="px-5 py-3">

                                        <span
                                            class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                            {{ $issue->status }}
                                        </span>

                                    </td>


                                    <td class="px-5 py-3">

                                        <a href="{{ route('issues.show', $issue) }}"
                                            class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white">
                                            View
                                        </a>

                                    </td>

                                </tr>

                                @empty

                                <tr>

                                    <td colspan="6" class="px-5 py-8 text-center text-sm text-slate-500">
                                        No issues found.
                                    </td>

                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <script>
    document
        .getElementById('issue-search')
        ?.addEventListener('input', function() {

            const query = this.value.toLowerCase();

            document
                .querySelectorAll('tbody tr')
                .forEach(row => {

                    row.classList.toggle(
                        'hidden',
                        !row.textContent.toLowerCase().includes(query)
                    );

                });

        });
    </script>

</x-app-layout>