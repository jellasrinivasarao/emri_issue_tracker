<x-app-layout>

    <x-slot name="header">

        <h2 class="text-xl font-semibold text-gray-800">

            Edit SLA Policy

        </h2>

    </x-slot>

    <div class="py-8">

        <div class="mx-auto max-w-6xl px-4">

            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">

                    <h3 class="text-lg font-semibold">

                        Update SLA Policy

                    </h3>

                    <p class="text-sm text-slate-600">

                        Modify SLA configuration.

                    </p>

                </div>

                <form method="POST" action="{{ route('sla-policies.update',$policy) }}" class="px-6 py-6">

                    @csrf
                    @method('PUT')

                    @include('admin.sla-policies._form')

                </form>

            </div>

        </div>

    </div>

</x-app-layout>