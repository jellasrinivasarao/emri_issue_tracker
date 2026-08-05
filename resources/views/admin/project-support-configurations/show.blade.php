<x-app-layout>

    <x-slot name="header">

        <h2 class="text-xl font-semibold text-gray-800">
            Support Configuration
        </h2>

    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-6xl space-y-6 px-4">


            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <h3 class="text-lg font-semibold text-slate-900">
                                {{ $configuration->config_name }}
                            </h3>

                            <p class="text-sm text-slate-500">
                                {{ $configuration->config_code }}
                            </p>

                        </div>

                        <span class="rounded-full px-3 py-1 text-xs font-semibold
                            {{ $configuration->is_active
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'bg-amber-50 text-amber-700' }}">
                            {{ $configuration->is_active ? 'Active' : 'Inactive' }}
                        </span>

                    </div>

                </div>


                <div class="grid gap-5 p-6 md:grid-cols-4">

                    <div>

                        <p class="text-xs uppercase text-slate-500">
                            Priority
                        </p>

                        <p class="mt-1 font-semibold">
                            {{ $configuration->default_priority }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs uppercase text-slate-500">
                            Auto Routing
                        </p>

                        <p class="mt-1 font-semibold">
                            {{ $configuration->auto_routing_enabled ? 'Enabled' : 'Disabled' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs uppercase text-slate-500">
                            Project ID
                        </p>

                        <p class="mt-1 font-semibold">
                            {{ $configuration->project_id ?? '-' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs uppercase text-slate-500">
                            Routing Rules
                        </p>

                        <p class="mt-1 font-semibold">
                            {{ $configuration->routingRules->count() }}
                        </p>

                    </div>

                </div>

            </div>


            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">

                    <h3 class="font-semibold">
                        Routing Rules
                    </h3>

                </div>


                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-slate-200">

                        <thead class="bg-purple-100">

                            <tr>

                                <th class="px-5 py-3 text-left text-xs uppercase">
                                    Rule
                                </th>

                                <th class="px-5 py-3 text-left text-xs uppercase">
                                    Team
                                </th>

                                <th class="px-5 py-3 text-left text-xs uppercase">
                                    Level
                                </th>

                                <th class="px-5 py-3 text-left text-xs uppercase">
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-200">

                            @forelse($configuration->routingRules as $rule)

                            <tr>

                                <td class="px-5 py-3">

                                    <div class="font-semibold">
                                        {{ $rule->rule_code }}
                                    </div>

                                    <div class="text-xs text-slate-500">
                                        {{ $rule->rule_name }}
                                    </div>

                                </td>

                                <td class="px-5 py-3 text-sm">
                                    {{ $rule->team?->team_name ?? '-' }}
                                </td>

                                <td class="px-5 py-3 text-sm">
                                    L{{ $rule->routing_level }}
                                </td>

                                <td class="px-5 py-3">

                                    <span
                                        class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                        {{ $rule->is_active ? 'Active' : 'Inactive' }}
                                    </span>

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td colspan="4" class="px-5 py-6 text-center text-sm text-slate-500">
                                    No routing rules configured.
                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>