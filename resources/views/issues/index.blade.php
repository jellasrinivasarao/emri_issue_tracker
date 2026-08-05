<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Create Issue
        </h2>
    </x-slot>

    <div class="py-8">

        <div class="mx-auto max-w-4xl px-4">

            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">

                    <h3 class="text-lg font-semibold text-slate-900">
                        Create Support Issue
                    </h3>

                    <p class="text-sm text-slate-600">
                        The issue will automatically be processed by the routing engine.
                    </p>

                </div>


                <form method="POST" action="{{ route('issues.store') }}" class="space-y-5 px-6 py-6">

                    @csrf


                    <div class="grid gap-5 md:grid-cols-2">

                        <div>

                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Project ID
                            </label>

                            <input type="number" name="project_id" value="{{ old('project_id') }}"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5">

                            @error('project_id')
                            <p class="mt-1 text-xs text-rose-600">
                                {{ $message }}
                            </p>
                            @enderror

                        </div>


                        <div>

                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Support Configuration
                            </label>

                            <select name="support_config_id" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5">

                                <option value="">
                                    Select Configuration
                                </option>

                                @foreach($configurations as $configuration)

                                <option value="{{ $configuration->support_config_id }}"
                                    @selected(old('support_config_id')==$configuration->support_config_id)
                                    >
                                    {{ $configuration->config_name }}
                                </option>

                                @endforeach

                            </select>

                        </div>

                    </div>


                    <div class="grid gap-5 md:grid-cols-2">

                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Issue Category
                            </label>

                            <input name="issue_category" value="{{ old('issue_category') }}"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5"
                                placeholder="Application / Network / Hardware">

                        </div>


                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Issue Type
                            </label>

                            <input name="issue_type" value="{{ old('issue_type') }}"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5"
                                placeholder="Login / API / Server">

                        </div>

                    </div>


                    <div>

                        <label class="mb-1 block text-sm font-medium">
                            Subject
                        </label>

                        <input name="subject" value="{{ old('subject') }}" required
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5"
                            placeholder="Enter issue subject">

                    </div>


                    <div>

                        <label class="mb-1 block text-sm font-medium">
                            Description
                        </label>

                        <textarea name="description" rows="6"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5"
                            placeholder="Describe the issue">{{ old('description') }}</textarea>

                    </div>


                    <div>

                        <label class="mb-1 block text-sm font-medium">
                            Priority
                        </label>

                        <select name="priority" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5">

                            <option value="LOW">Low</option>
                            <option value="MEDIUM" selected>Medium</option>
                            <option value="HIGH">High</option>
                            <option value="CRITICAL">Critical</option>

                        </select>

                    </div>


                    <div class="flex justify-end gap-3 border-t border-slate-200 pt-5">

                        <a href="{{ route('issues.index') }}"
                            class="rounded-xl border border-slate-300 bg-slate-100 px-5 py-2.5 text-sm font-semibold">
                            Cancel
                        </a>

                        <button type="submit"
                            class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                            Create & Route Issue
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>