@extends('layouts.app')

@section('title', 'Create Requirement')

@section('page-title', 'Create Requirement')

@section('content')

<div class="mx-auto max-w-5xl">

<form
    method="POST"
    action="{{ route('requirements.store') }}"
    enctype="multipart/form-data"
    class="space-y-6">

    @csrf


    {{-- Basic Information --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">

            <h2 class="font-semibold text-slate-900">
                Requirement Information
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Enter requirement details.
            </p>

        </div>


        <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

            <div class="md:col-span-2">

                <label class="mb-2 block text-sm font-semibold">
                    Title *
                </label>

                <input
                    name="title"
                    value="{{ old('title') }}"
                    required
                    class="w-full rounded-xl border-slate-300">

                @error('title')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            <div class="md:col-span-2">

                <label class="mb-2 block text-sm font-semibold">
                    Brief Description *
                </label>

                <textarea
                    name="description"
                    rows="5"
                    required
                    class="w-full rounded-xl border-slate-300">{{ old('description') }}</textarea>

            </div>


            <div>

                <label class="mb-2 block text-sm font-semibold">
                    State *
                </label>

                <select
                    name="state_id"
                    required
                    class="w-full rounded-xl border-slate-300">

                    <option value="">
                        Select State
                    </option>

                    @foreach($states as $state)

                        <option
                            value="{{ $state->state_id }}"
                            @selected(old('state_id') == $state->state_id)>

                            {{ $state->state_name }}

                        </option>

                    @endforeach

                </select>

            </div>


            <div>

                <label class="mb-2 block text-sm font-semibold">
                    Project *
                </label>

                <select
                    name="project_id"
                    required
                    class="w-full rounded-xl border-slate-300">

                    <option value="">
                        Select Project
                    </option>

                    @foreach($projects as $project)

                        <option
                            value="{{ $project->project_id }}"
                            @selected(old('project_id') == $project->project_id)>

                            {{ $project->project_name }}

                        </option>

                    @endforeach

                </select>

            </div>


            <div>

                <label class="mb-2 block text-sm font-semibold">
                    BRD Raised By
                </label>

                <input
                    name="brd_raised_by"
                    value="{{ old('brd_raised_by') }}"
                    class="w-full rounded-xl border-slate-300">

            </div>


            <div>

                <label class="mb-2 block text-sm font-semibold">
                    HO IT Team
                </label>

                <div class="flex gap-5 pt-2">

                    <label>
                        <input
                            type="radio"
                            name="ho_it_team"
                            value="1"
                            checked>

                        Yes
                    </label>

                    <label>
                        <input
                            type="radio"
                            name="ho_it_team"
                            value="0">

                        No
                    </label>

                </div>

            </div>

        </div>

    </div>


    {{-- BRD --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">

            <h2 class="font-semibold">
                BRD Document *
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                PDF, DOCX or XLSX. Maximum 25 MB.
            </p>

        </div>


        <div class="p-6">

            <label
                class="flex cursor-pointer flex-col items-center justify-center
                       rounded-2xl border-2 border-dashed border-blue-200
                       bg-blue-50 p-12 text-center">

                <div class="text-blue-900">

                    <svg
                        class="mx-auto h-10 w-10"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M12 16V4m0 0L8 8m4-4l4 4M5 20h14"/>

                    </svg>

                </div>

                <span class="mt-3 font-semibold">
                    Upload BRD
                </span>

                <span class="mt-1 text-xs text-slate-500">
                    Click to browse
                </span>

                <input
                    type="file"
                    name="brd_document"
                    accept=".pdf,.docx,.xlsx"
                    required
                    class="hidden">

            </label>

        </div>

    </div>


    {{-- Dates --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <div class="grid gap-6 md:grid-cols-2">

            <div>

                <label class="mb-2 block text-sm font-semibold">
                    Received to HO with BRD
                </label>

                <input
                    type="date"
                    name="received_at"
                    value="{{ old('received_at') }}"
                    class="w-full rounded-xl border-slate-300">

            </div>


            <div>

                <label class="mb-2 block text-sm font-semibold">
                    Requested to Achala
                </label>

                <input
                    type="date"
                    name="requested_to_vendor_at"
                    value="{{ old('requested_to_vendor_at') }}"
                    class="w-full rounded-xl border-slate-300">

            </div>


            <div class="md:col-span-2">

                <label class="mb-2 block text-sm font-semibold">
                    Additional Details
                </label>

                <textarea
                    name="additional_details"
                    rows="4"
                    class="w-full rounded-xl border-slate-300">{{ old('additional_details') }}</textarea>

            </div>

        </div>

    </div>


    {{-- Actions --}}
    <div class="flex justify-end gap-3">

        <a
            href="{{ route('requirements.index') }}"
            class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold">

            Cancel

        </a>


        <button
            type="submit"
            class="rounded-xl bg-blue-900 px-5 py-2.5 text-sm font-semibold text-white">

            Submit to Achala →

        </button>

    </div>

</form>

</div>

@endsection