<form id="raiseIssueForm" method="POST" action="{{ route('issues.store') }}" enctype="multipart/form-data"
    class="p-5 sm:p-6">
    @csrf

    {{-- SERVICE INFORMATION --}}
    <div class="rounded-xl border border-slate-200 bg-white">

        <div class="flex items-center gap-3 border-b border-slate-200 px-5 py-3">

            <span class="flex h-7 w-7 items-center justify-center
                         rounded-full bg-blue-600 text-xs font-bold text-white">
                1
            </span>

            <h3 class="text-sm font-bold uppercase tracking-wide text-blue-700">
                Service Information
            </h3>

        </div>

        <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2">

            {{-- State --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    State <span class="text-red-500">*</span>
                </label>

                <select name="state_id" id="state_id" class="mt-1 block w-full rounded-lg border-slate-300
                           text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select State</option>

                    @foreach($states ?? [] as $state)
                    <option value="{{ $state->id }}">
                        {{ $state->name }}
                    </option>
                    @endforeach

                </select>

                @error('state_id')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
                @enderror
            </div>


            {{-- Service --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Service <span class="text-red-500">*</span>
                </label>

                <select name="service_id" id="service_id" class="mt-1 block w-full rounded-lg border-slate-300
                           text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select Service</option>

                    @foreach($services ?? [] as $service)
                    <option value="{{ $service->id }}">
                        {{ $service->name }}
                    </option>
                    @endforeach

                </select>
            </div>


            {{-- Project --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Project <span class="text-red-500">*</span>
                </label>

                <select name="project_id" id="project_id" class="mt-1 block w-full rounded-lg border-slate-300
                           text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select Project</option>
                </select>
            </div>


            {{-- Application --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Application <span class="text-red-500">*</span>
                </label>

                <select name="application_id" id="application_id" class="mt-1 block w-full rounded-lg border-slate-300
                           text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select Application</option>
                </select>
            </div>


            {{-- Module --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Module
                </label>

                <select name="module_id" id="module_id" class="mt-1 block w-full rounded-lg border-slate-300
                           text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select Module</option>
                </select>
            </div>

        </div>
    </div>


    {{-- ISSUE INFORMATION --}}
    <div class="mt-4 rounded-xl border border-slate-200 bg-white">

        <div class="flex items-center gap-3 border-b border-slate-200 px-5 py-3">

            <span class="flex h-7 w-7 items-center justify-center
                         rounded-full bg-blue-600 text-xs font-bold text-white">
                2
            </span>

            <h3 class="text-sm font-bold uppercase tracking-wide text-blue-700">
                Issue Information
            </h3>

        </div>


        <div class="grid grid-cols-1 gap-4 p-5">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                {{-- Category --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700">
                        Issue Category <span class="text-red-500">*</span>
                    </label>

                    <select name="issue_category_id" class="mt-1 block w-full rounded-lg border-slate-300
                               text-sm">
                        <option value="">Select Category</option>
                    </select>
                </div>


                {{-- Priority --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700">
                        Priority <span class="text-red-500">*</span>
                    </label>

                    <select name="priority" class="mt-1 block w-full rounded-lg border-slate-300
                               text-sm">
                        <option value="">Select Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>

            </div>


            {{-- Subject --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Subject <span class="text-red-500">*</span>
                </label>

                <input type="text" name="subject" maxlength="255" placeholder="Enter issue subject" class="mt-1 block w-full rounded-lg border-slate-300
                           text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>


            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Description <span class="text-red-500">*</span>
                </label>

                <textarea name="description" rows="5"
                    placeholder="Describe the issue, operational impact and observations" class="mt-1 block w-full rounded-lg border-slate-300
                           text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>

        </div>
    </div>


    {{-- SUPPORTING INFORMATION --}}
    <div class="mt-4 rounded-xl border border-slate-200 bg-white">

        <div class="flex items-center gap-3 border-b border-slate-200 px-5 py-3">

            <span class="flex h-7 w-7 items-center justify-center
                         rounded-full bg-blue-600 text-xs font-bold text-white">
                3
            </span>

            <h3 class="text-sm font-bold uppercase tracking-wide text-blue-700">
                Supporting Information
            </h3>

        </div>


        <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2">

            {{-- Occurred On --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Occurred On <span class="text-red-500">*</span>
                </label>

                <input type="datetime-local" name="occurred_on" class="mt-1 block w-full rounded-lg border-slate-300
                           text-sm">
            </div>


            {{-- Affected Users --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Affected Users
                </label>

                <input type="text" name="affected_users" placeholder="Enter usernames or user groups" class="mt-1 block w-full rounded-lg border-slate-300
                           text-sm">
            </div>


            {{-- Attachment --}}
            <div class="md:col-span-2">

                <label class="block text-sm font-medium text-slate-700">
                    Attachment
                </label>

                <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xlsx,.xls" class="mt-1 block w-full rounded-lg border border-slate-300
                           bg-white text-sm">

                <p class="mt-1 text-xs text-slate-500">
                    Allowed file types: jpg, jpeg, png, pdf, doc, docx,
                    xlsx. Maximum size: 10 MB.
                </p>

            </div>

        </div>
    </div>


    {{-- FOOTER --}}
    <div class="mt-5 flex items-center justify-end gap-3">

        <button type="button" onclick="closeRaiseIssueModal()" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5
                   text-sm font-medium text-slate-700 hover:bg-slate-50">
            Cancel
        </button>

        <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm
                   font-semibold text-white shadow-sm
                   hover:bg-blue-700">
            Submit Issue
        </button>

    </div>

</form>