<x-app-layout>

    <x-slot name="header">

        <h2 class="text-xl font-semibold text-gray-800">
            Edit Project Support Configuration
        </h2>

    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-3xl px-4">

            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">

                <form method="POST" action="{{ route('project.support.update', $configuration) }}"
                    class="space-y-5 p-6">

                    @csrf
                    @method('PUT')


                    <div class="grid gap-5 md:grid-cols-2">

                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Configuration Code
                            </label>

                            <input name="config_code" required
                                value="{{ old('config_code', $configuration->config_code) }}"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 uppercase">

                        </div>


                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Configuration Name
                            </label>

                            <input name="config_name" required
                                value="{{ old('config_name', $configuration->config_name) }}"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5">

                        </div>

                    </div>


                    <div>

                        <label class="mb-1 block text-sm font-medium">
                            Project ID
                        </label>

                        <input type="number" name="project_id"
                            value="{{ old('project_id', $configuration->project_id) }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5">

                    </div>


                    <div>

                        <label class="mb-1 block text-sm font-medium">
                            Description
                        </label>

                        <textarea name="description" rows="4"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5">{{ old('description', $configuration->description) }}</textarea>

                    </div>


                    <div>

                        <label class="mb-1 block text-sm font-medium">
                            Default Priority
                        </label>

                        <select name="default_priority" class="w-full rounded-xl border border-slate-200 px-3 py-2.5">

                            @foreach(['LOW','MEDIUM','HIGH','CRITICAL'] as $priority)

                            <option value="{{ $priority }}" @selected($configuration->default_priority === $priority)
                                >
                                {{ ucfirst(strtolower($priority)) }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    <label class="flex items-center gap-2">

                        <input type="checkbox" name="auto_routing_enabled" value="1"
                            @checked($configuration->auto_routing_enabled)
                        class="rounded border-slate-300"
                        >

                        <span class="text-sm font-medium">
                            Enable Automatic Routing
                        </span>

                    </label>


                    <div class="flex justify-end gap-3 border-t border-slate-200 pt-5">

                        <a href="{{ route('project.support') }}"
                            class="rounded-xl bg-slate-100 px-5 py-2.5 text-sm font-semibold">
                            Cancel
                        </a>

                        <button type="submit"
                            class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white">
                            Update
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>