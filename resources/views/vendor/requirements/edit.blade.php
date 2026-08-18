@extends('layouts.app')

@section('title', 'Update Requirement')

@section('page-title', 'Update Timeline & Status')

@section('content')

<div class="mx-auto max-w-5xl">

    <form
        method="POST"
        action="{{ route(
            'vendor.requirements.update',
            $requirement
        ) }}"
        class="space-y-6">

        @csrf

        @method('PUT')


        {{-- Read Only --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-5">

                <h2 class="font-semibold">
                    Requirement
                </h2>

            </div>


            <div class="grid gap-6 p-6 md:grid-cols-2">

                <div>

                    <p class="text-xs uppercase text-slate-400">
                        Requirement
                    </p>

                    <p class="mt-1 font-semibold">
                        {{ $requirement->requirement_no }}
                    </p>

                </div>


                <div>

                    <p class="text-xs uppercase text-slate-400">
                        Title
                    </p>

                    <p class="mt-1 font-semibold">
                        {{ $requirement->title }}
                    </p>

                </div>


                <div>

                    <p class="text-xs uppercase text-slate-400">
                        State
                    </p>

                    <p class="mt-1">
                        {{ $requirement->state->name ?? '-' }}
                    </p>

                </div>


                <div>

                    <p class="text-xs uppercase text-slate-400">
                        Project
                    </p>

                    <p class="mt-1">
                        {{ $requirement->project->name ?? '-' }}
                    </p>

                </div>


                <div class="md:col-span-2">

                    <p class="text-xs uppercase text-slate-400">
                        Description
                    </p>

                    <p class="mt-2 whitespace-pre-line text-sm leading-6">
                        {{ $requirement->description }}
                    </p>

                </div>


                <div class="md:col-span-2">

                    @foreach($requirement->files as $file)

                        <a
                            href="{{ route(
                                'requirements.files.download',
                                [$requirement, $file->id]
                            ) }}"
                            class="inline-flex rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold">

                            Download BRD:
                            {{ $file->original_name }}

                        </a>

                    @endforeach

                </div>

            </div>

        </div>


        {{-- Vendor Inputs --}}
        <div class="rounded-2xl border border-teal-200 bg-white shadow-sm">

            <div class="border-b border-teal-100 bg-teal-50 px-6 py-5">

                <h2 class="font-semibold text-teal-900">
                    Achala Inputs
                </h2>

            </div>


            <div class="grid gap-6 p-6">


                <div>

                    <label class="mb-2 block text-sm font-semibold">
                        Man Days *
                    </label>

                    <input
                        type="number"
                        step="0.5"
                        min="0"
                        name="man_days"
                        value="{{ old(
                            'man_days',
                            $requirement->man_days
                        ) }}"
                        required
                        class="w-full rounded-xl border-slate-300">

                </div>


                <div>

                    <label class="mb-2 block text-sm font-semibold">
                        Timeline / Plan *
                    </label>

                    <textarea
                        name="timeline"
                        rows="5"
                        required
                        placeholder="Development: 8 days&#10;Testing: 3 days&#10;UAT Ready: 28-Aug-2026"
                        class="w-full rounded-xl border-slate-300">{{ old(
                            'timeline',
                            $requirement->timeline
                        ) }}</textarea>

                </div>


                <div>

                    <label class="mb-3 block text-sm font-semibold">
                        Delivery Status *
                    </label>

                    <div class="grid gap-3 sm:grid-cols-2">

                        @foreach([

                            'Requirements Understood',

                            'Development Started',

                            'Development Completed',

                            'Moved to UAT',

                            'UAT Completed',

                            'Moved to Production',

                            'On Hold',

                        ] as $status)

                            <label class="flex items-center gap-3 rounded-xl border border-slate-200 p-3">

                                <input
                                    type="radio"
                                    name="delivery_status"
                                    value="{{ $status }}"
                                    @checked(
                                        old(
                                            'delivery_status',
                                            $requirement->delivery_status
                                        ) === $status
                                    )>

                                <span class="text-sm">
                                    {{ $status }}
                                </span>

                            </label>

                        @endforeach

                    </div>

                </div>


                <div>

                    <label class="mb-2 block text-sm font-semibold">
                        Remarks for HO
                    </label>

                    <textarea
                        name="remarks"
                        rows="4"
                        class="w-full rounded-xl border-slate-300">{{ old(
                            'remarks',
                            $requirement->vendor_remarks
                        ) }}</textarea>

                </div>

            </div>

        </div>


        <div class="flex justify-end gap-3">

            <button
                type="submit"
                class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold">

                Save Update

            </button>


            <button
                type="submit"
                class="rounded-xl bg-teal-700 px-5 py-2.5 text-sm font-semibold text-white">

                Submit Update →

            </button>

        </div>

    </form>

</div>

@endsection