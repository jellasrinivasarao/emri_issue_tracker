@extends('layouts.app')

@section('title', 'Create Requirement')

@section('page-title', 'Create Requirement')

@section('page-description')
    Raise a new requirement and submit it to Achala
@endsection


@section('content')

<div class="mx-auto max-w-5xl">

    <form
        method="POST"
        action="{{ route('requirements.store') }}"
        enctype="multipart/form-data"
        class="space-y-6">

        @csrf


        {{-- Requirement Information --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-5">

                <h2 class="font-semibold text-slate-900">
                    Requirement Information
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Enter the basic requirement details.
                </p>

            </div>


            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">


                {{-- Title --}}
                <div class="md:col-span-2">

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Title <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        placeholder="Enter requirement title"
                        required
                        class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm
                               focus:border-blue-800 focus:ring-blue-800">

                    @error('title')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Description --}}
                <div class="md:col-span-2">

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Brief Description <span class="text-red-500">*</span>
                    </label>

                    <textarea
                        name="description"
                        rows="4"
                        required
                        placeholder="Describe the requirement..."
                        class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm
                               focus:border-blue-800 focus:ring-blue-800">{{ old('description') }}</textarea>

                </div>


                {{-- State --}}
                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        State <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="state_id"
                        required
                        class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm
                               focus:border-blue-800 focus:ring-blue-800">

                        <option value="">Select State</option>

                        @foreach($states as $state)

                            <option
                                value="{{ $state->id }}"
                                @selected(old('state_id') == $state->id)>

                                {{ $state->name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Project --}}
                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Project <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="project_id"
                        required
                        class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm
                               focus:border-blue-800 focus:ring-blue-800">

                        <option value="">Select Project</option>

                        @foreach($projects as $project)

                            <option
                                value="{{ $project->id }}"
                                @selected(old('project_id') == $project->id)>

                                {{ $project->name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- BRD Raised By --}}
                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        BRD Raised By
                    </label>

                    <input
                        type="text"
                        name="brd_raised_by"
                        value="{{ old('brd_raised_by') }}"
                        class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm
                               focus:border-blue-800 focus:ring-blue-800">

                </div>


                {{-- HO Team --}}
                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        HO IT Team
                    </label>

                    <div class="flex gap-6 pt-3">

                        <label class="flex items-center gap-2 text-sm">

                            <input
                                type="radio"
                                name="ho_it_team"
                                value="1"
                                checked
                                class="text-blue-800 focus:ring-blue-800">

                            Yes

                        </label>


                        <label class="flex items-center gap-2 text-sm">

                            <input
                                type="radio"
                                name="ho_it_team"
                                value="0"
                                class="text-blue-800 focus:ring-blue-800">

                            No

                        </label>

                    </div>

                </div>

            </div>

        </div>


        {{-- BRD Upload --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-5">

                <h2 class="font-semibold text-slate-900">
                    BRD Document
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Upload PDF, DOCX or XLSX document. Maximum size 25 MB.
                </p>

            </div>


            <div class="p-6">

                <label
                    class="flex cursor-pointer flex-col items-center justify-center rounded-2xl
                           border-2 border-dashed border-blue-200 bg-blue-50/50 px-6 py-12
                           transition hover:border-blue-400 hover:bg-blue-50">

                    <svg class="h-10 w-10 text-blue-800"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="1.7"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6H16a5 5 0 011 9.9M12 12v8m0-8l-3 3m3-3l3 3"/>

                    </svg>


                    <span class="mt-4 text-sm font-semibold text-slate-700">
                        Drag & drop your BRD here
                    </span>

                    <span class="mt-1 text-xs text-slate-500">
                        or click to browse
                    </span>


                    <input
                        type="file"
                        name="brd_document"
                        required
                        accept=".pdf,.docx,.xlsx"
                        class="hidden">

                </label>

            </div>

        </div>


        {{-- Dates / Additional --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Received to HO with BRD
                    </label>

                    <input
                        type="date"
                        name="received_at"
                        value="{{ old('received_at') }}"
                        class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm">

                </div>


                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Requested to Achala
                    </label>

                    <input
                        type="date"
                        name="requested_to_vendor_at"
                        value="{{ old('requested_to_vendor_at') }}"
                        class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm">

                </div>


                <div class="md:col-span-2">

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Additional Details
                    </label>

                    <textarea
                        name="additional_details"
                        rows="4"
                        class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm">{{ old('additional_details') }}</textarea>

                </div>

            </div>

        </div>


        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">

            <a
                href="{{ route('requirements.index') }}"
                class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">

                Cancel

            </a>


            <button
                type="submit"
                class="rounded-xl bg-blue-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">

                Submit to Achala
                <span class="ml-1">→</span>

            </button>

        </div>

    </form>

</div>

@endsection