@extends('layouts.app')

@section('title', $requirement->requirement_no)

@section('page-title', $requirement->requirement_no)

@section('content')

<div class="mx-auto max-w-6xl space-y-6">


    {{-- Header --}}
    <div class="flex items-start justify-between">

        <div>

            <p class="text-sm text-slate-500">
                {{ $requirement->requirement_no }}
            </p>

            <h1 class="mt-1 text-2xl font-bold text-slate-900">
                {{ $requirement->title }}
            </h1>

        </div>


        @include(
            'components.status-badge',
            ['status' => $requirement->status]
        )

    </div>


    {{-- Information --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">


        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-5">

                <h2 class="font-semibold">
                    Requirement Details
                </h2>

            </div>


            <div class="grid gap-6 p-6 md:grid-cols-2">

                <div>

                    <p class="text-xs font-semibold uppercase text-slate-400">
                        State
                    </p>

                    <p class="mt-1 text-sm">
                        {{ $requirement->state->name ?? '-' }}
                    </p>

                </div>


                <div>

                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Project
                    </p>

                    <p class="mt-1 text-sm">
                        {{ $requirement->project->name ?? '-' }}
                    </p>

                </div>


                <div class="md:col-span-2">

                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Description
                    </p>

                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">
                        {{ $requirement->description }}
                    </p>

                </div>

            </div>

        </div>


        {{-- Vendor --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <p class="text-xs font-semibold uppercase text-slate-400">
                Assigned Vendor
            </p>

            <p class="mt-2 font-semibold">
                {{ $requirement->vendor->name ?? 'Not Assigned' }}
            </p>


            <div class="mt-6">

                <p class="text-xs font-semibold uppercase text-slate-400">
                    Man Days
                </p>

                <p class="mt-2 text-2xl font-bold">
                    {{ $requirement->man_days ?? '-' }}
                </p>

            </div>

        </div>

    </div>


    {{-- BRD --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">

            <h2 class="font-semibold">
                BRD Documents
            </h2>

        </div>


        <div class="divide-y divide-slate-100">

            @forelse($requirement->files as $file)

                <div class="flex items-center justify-between px-6 py-4">

                    <div>

                        <p class="text-sm font-semibold">
                            {{ $file->original_name }}
                        </p>

                        <p class="text-xs text-slate-500">
                            {{ number_format(($file->file_size ?? 0) / 1024, 1) }} KB
                        </p>

                    </div>


                    <a
                        href="{{ route(
                            'requirements.files.download',
                            [
                                $requirement,
                                $file->id
                            ]
                        ) }}"
                        class="rounded-xl bg-blue-900 px-4 py-2 text-xs font-semibold text-white">

                        Download

                    </a>

                </div>

            @empty

                <div class="px-6 py-8 text-center text-sm text-slate-500">
                    No documents.
                </div>

            @endforelse

        </div>

    </div>


    {{-- Status History --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">

            <h2 class="font-semibold">
                Status History
            </h2>

        </div>


        <div class="p-6">

            <div class="space-y-6">

                @foreach($requirement->statusHistory as $history)

                    <div class="relative pl-6">

                        <span
                            class="absolute left-0 top-1.5 h-2.5 w-2.5 rounded-full bg-blue-700">
                        </span>


                        <p class="text-sm font-semibold">

                            {{ $history->to_status }}

                        </p>


                        @if($history->from_status)

                            <p class="text-xs text-slate-500">

                                {{ $history->from_status }}
                                →
                                {{ $history->to_status }}

                            </p>

                        @endif


                        @if($history->remarks)

                            <p class="mt-1 text-sm text-slate-600">

                                {{ $history->remarks }}

                            </p>

                        @endif


                        <p class="mt-1 text-xs text-slate-400">

                            {{ $history->user->name ?? 'System' }}
                            ·
                            {{ $history->created_at->format('d M Y H:i') }}

                        </p>

                    </div>

                @endforeach

            </div>

        </div>

    </div>

</div>

@endsection