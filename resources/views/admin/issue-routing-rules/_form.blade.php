@php
$isEdit = isset($issueRoutingRule);

$formAction = $isEdit
? route('admin.issue-routing-rules.update', $issueRoutingRule)
: route('admin.issue-routing-rules.store');

$selectedProject = old(
'project_id',
$issueRoutingRule->project_id ?? ''
);

$selectedConfig = old(
'support_config_id',
$issueRoutingRule->support_config_id ?? ''
);

$selectedApplication = old(
'application_id',
$issueRoutingRule->application_id ?? ''
);

$selectedState = old(
'state_id',
$issueRoutingRule->state_id ?? ''
);

$selectedTargetType = old(
'target_type',
$targetType ?? ''
);

$selectedTargetId = old(
'target_id',
!empty($issueRoutingRule->hoit_id)
? $issueRoutingRule->hoit_id
: ($issueRoutingRule->vendor_id ?? '')
);
@endphp

<form method="POST" action="{{ $formAction }}" id="routingRuleForm">

    @csrf

    @if($isEdit)
    @method('PUT')
    @endif


    {{-- ============================================================
        VALIDATION ERRORS
    ============================================================= --}}

    @if($errors->any())

    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4">

        <div class="flex items-start gap-3">

            <svg class="mt-0.5 h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M10.29 3.86l-7.82 13a2 2 0 001.71 2.64h15.64a2 2 0 001.71-2.64l-7.82-13a2 2 0 00-3.42 0z" />
            </svg>

            <div>
                <h3 class="font-semibold text-red-800">
                    Please correct the following errors
                </h3>

                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">

                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach

                </ul>
            </div>

        </div>

    </div>

    @endif


    {{-- ============================================================
        RULE INFORMATION
    ============================================================= --}}

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50">

                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H5a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2h-4M9 5a3 3 0 006 0M9 5h6" />
                    </svg>

                </div>

                <div>
                    <h3 class="text-base font-semibold text-slate-900">
                        Rule Information
                    </h3>

                    <p class="text-sm text-slate-500">
                        Define the conditions used by the issue routing engine.
                    </p>
                </div>

            </div>

        </div>


        <div class="p-6">

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                {{-- Rule Code --}}
                <div>

                    <label for="rule_code" class="mb-2 block text-sm font-medium text-slate-700">
                        Rule Code
                        <span class="text-red-500">*</span>
                    </label>

                    <input type="text" id="rule_code" name="rule_code"
                        value="{{ old('rule_code', $issueRoutingRule->rule_code ?? '') }}" maxlength="50" required
                        placeholder="Example: ROUTE-001"
                        class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    @error('rule_code')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                    @enderror

                </div>


                {{-- Rule Name --}}
                <div>

                    <label for="rule_name" class="mb-2 block text-sm font-medium text-slate-700">
                        Rule Name
                        <span class="text-red-500">*</span>
                    </label>

                    <input type="text" id="rule_name" name="rule_name"
                        value="{{ old('rule_name', $issueRoutingRule->rule_name ?? '') }}" maxlength="150" required
                        placeholder="Example: High Priority Payment Issue"
                        class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    @error('rule_name')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                    @enderror

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
        ROUTING SCOPE
    ============================================================= --}}

    <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">

            <h3 class="text-base font-semibold text-slate-900">
                Routing Scope
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Select the project and business context for this routing rule.
            </p>

        </div>


        <div class="p-6">

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">


                {{-- Project --}}
                <div>

                    <label for="project_id" class="mb-2 block text-sm font-medium text-slate-700">
                        Project
                        <span class="text-red-500">*</span>
                    </label>

                    <select id="project_id" name="project_id" required
                        class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                        <option value="">
                            Select Project
                        </option>

                        @foreach($projects as $project)

                        <option value="{{ $project->project_id }}" @selected( $selectedProject==$project->project_id
                            )
                            >
                            {{ $project->project_code }}
                            -
                            {{ $project->project_name }}
                        </option>

                        @endforeach

                    </select>

                    @error('project_id')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                    @enderror

                </div>


                {{-- Support Configuration --}}
                <div>

                    <label for="support_config_id" class="mb-2 block text-sm font-medium text-slate-700">
                        Support Configuration
                    </label>

                    <select id="support_config_id" name="support_config_id"
                        class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-slate-100">

                        <option value="">
                            Select Support Configuration
                        </option>

                    </select>

                    <p id="configLoading" class="mt-1 hidden text-xs text-slate-500">
                        Loading configurations...
                    </p>

                    @error('support_config_id')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                    @enderror

                </div>


                {{-- Application --}}
                <div>

                    <label for="application_id" class="mb-2 block text-sm font-medium text-slate-700">
                        Application
                    </label>

                    <select id="application_id" name="application_id"
                        class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-slate-100">

                        <option value="">
                            Select Application
                        </option>

                    </select>

                    <p id="applicationLoading" class="mt-1 hidden text-xs text-slate-500">
                        Loading applications...
                    </p>

                    @error('application_id')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                    @enderror

                </div>


                {{-- State --}}
                <div>

                    <label for="state_id" class="mb-2 block text-sm font-medium text-slate-700">
                        State
                    </label>

                    <select id="state_id" name="state_id"
                        class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-slate-100">

                        <option value="">
                            Select State
                        </option>

                    </select>

                    <p id="stateLoading" class="mt-1 hidden text-xs text-slate-500">
                        Loading states...
                    </p>

                    @error('state_id')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                    @enderror

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
        ISSUE MATCHING
    ============================================================= --}}

    <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">

            <h3 class="text-base font-semibold text-slate-900">
                Issue Matching Criteria
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                These fields determine whether this rule matches an issue.
            </p>

        </div>


        <div class="p-6">

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

                {{-- Issue Category --}}
                <div>

                    <label for="issue_category" class="mb-2 block text-sm font-medium text-slate-700">
                        Issue Category
                    </label>

                    <input type="text" id="issue_category" name="issue_category"
                        value="{{ old('issue_category', $issueRoutingRule->issue_category ?? '') }}" maxlength="100"
                        placeholder="Example: Payment"
                        class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                </div>


                {{-- Issue Type --}}
                <div>

                    <label for="issue_type" class="mb-2 block text-sm font-medium text-slate-700">
                        Issue Type
                    </label>

                    <input type="text" id="issue_type" name="issue_type"
                        value="{{ old('issue_type', $issueRoutingRule->issue_type ?? '') }}" maxlength="100"
                        placeholder="Example: Transaction Failure"
                        class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                </div>


                {{-- Priority --}}
                <div>

                    <label for="priority" class="mb-2 block text-sm font-medium text-slate-700">
                        Priority
                    </label>

                    <select id="priority" name="priority"
                        class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                        <option value="">
                            Any Priority
                        </option>

                        @foreach([
                        'Critical',
                        'High',
                        'Medium',
                        'Low'
                        ] as $priority)

                        <option value="{{ $priority }}" @selected( old( 'priority' , $issueRoutingRule->priority ?? ''
                            ) === $priority
                            )
                            >
                            {{ $priority }}
                        </option>

                        @endforeach

                    </select>

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
        ROUTING TARGET
    ============================================================= --}}

    <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">

            <h3 class="text-base font-semibold text-slate-900">
                Routing Target
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Define the level and destination for matched issues.
            </p>

        </div>


        <div class="p-6">

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

                {{-- Routing Level --}}
                <div>

                    <label for="routing_level" class="mb-2 block text-sm font-medium text-slate-700">
                        Routing Level
                        <span class="text-red-500">*</span>
                    </label>

                    <select id="routing_level" name="routing_level" required
                        class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                        @foreach([1,2,3,4,5] as $level)

                        <option value="{{ $level }}" @selected( old( 'routing_level' , $issueRoutingRule->routing_level
                            ?? 1
                            ) == $level
                            )
                            >
                            Level {{ $level }}
                        </option>

                        @endforeach

                    </select>

                </div>


                {{-- Target Type --}}
                <div>

                    <label for="target_type" class="mb-2 block text-sm font-medium text-slate-700">
                        Target Type
                    </label>

                    <select id="target_type" name="target_type"
                        class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                        <option value="">
                            Select Target Type
                        </option>

                        <option value="HOIT" @selected($selectedTargetType==='HOIT' )>
                            HO IT
                        </option>

                        <option value="VENDOR" @selected($selectedTargetType==='VENDOR' )>
                            Vendor
                        </option>

                    </select>

                </div>


                {{-- Target --}}
                <div>

                    <label for="target_id" class="mb-2 block text-sm font-medium text-slate-700">
                        Target
                    </label>

                    <select id="target_id" name="target_id"
                        class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-slate-100">

                        <option value="">
                            Select Target
                        </option>

                    </select>

                    <p id="targetLoading" class="mt-1 hidden text-xs text-slate-500">
                        Loading targets...
                    </p>

                </div>

            </div>


            {{-- Hidden database fields --}}
            <input type="hidden" name="vendor_id" id="vendor_id"
                value="{{ old('vendor_id', $issueRoutingRule->vendor_id ?? '') }}">

            <input type="hidden" name="hoit_id" id="hoit_id"
                value="{{ old('hoit_id', $issueRoutingRule->hoit_id ?? '') }}">

        </div>

    </div>


    {{-- ============================================================
        SUPPORT TEAM
    ============================================================= --}}

    <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">

            <h3 class="text-base font-semibold text-slate-900">
                Support Team
            </h3>

        </div>


        <div class="p-6">

            <div class="max-w-xl">

                <label for="support_team_id" class="mb-2 block text-sm font-medium text-slate-700">
                    Support Team
                </label>

                <select id="support_team_id" name="support_team_id"
                    class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    <option value="">
                        Select Support Team
                    </option>

                    @foreach($supportTeams as $team)

                    <option value="{{ $team->support_team_id }}" @selected( old( 'support_team_id' , $issueRoutingRule->
                        support_team_id ?? ''
                        ) == $team->support_team_id
                        )
                        >
                        {{ $team->team_code }}
                        -
                        {{ $team->team_name }}
                    </option>

                    @endforeach

                </select>

            </div>

        </div>

    </div>


    {{-- ============================================================
        STATUS
    ============================================================= --}}

    <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="p-6">

            <div class="flex flex-wrap gap-8">

                {{-- Default --}}
                <label class="flex cursor-pointer items-center gap-3">

                    <input type="hidden" name="is_default" value="0">

                    <input type="checkbox" name="is_default" value="1" @checked( old( 'is_default' ,
                        $issueRoutingRule->is_default ?? false
                    )
                    )
                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                    >

                    <span>
                        <span class="block text-sm font-medium text-slate-700">
                            Default Rule
                        </span>

                        <span class="block text-xs text-slate-500">
                            Use this rule when no more specific rule matches.
                        </span>
                    </span>

                </label>


                {{-- Active --}}
                <label class="flex cursor-pointer items-center gap-3">

                    <input type="hidden" name="is_active" value="0">

                    <input type="checkbox" name="is_active" value="1" @checked( old( 'is_active' ,
                        $issueRoutingRule->is_active ?? true
                    )
                    )
                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                    >

                    <span>
                        <span class="block text-sm font-medium text-slate-700">
                            Active
                        </span>

                        <span class="block text-xs text-slate-500">
                            Only active rules are considered by routing.
                        </span>
                    </span>

                </label>

            </div>

        </div>

    </div>


    {{-- ============================================================
        ACTIONS
    ============================================================= --}}

    <div class="mt-6 flex items-center justify-end gap-3">

        <a href="{{ route('admin.issue-routing-rules.index') }}"
            class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Cancel
        </a>

        <button type="submit" id="saveRuleButton"
            class="rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">

            {{ $isEdit ? 'Update Routing Rule' : 'Save Routing Rule' }}

        </button>

    </div>

</form>


{{-- ================================================================
    DEPENDENT DROPDOWN JAVASCRIPT
================================================================ --}}

<script>
document.addEventListener('DOMContentLoaded', function() {

    const projectSelect =
        document.getElementById('project_id');

    const configSelect =
        document.getElementById('support_config_id');

    const applicationSelect =
        document.getElementById('application_id');

    const stateSelect =
        document.getElementById('state_id');

    const targetType =
        document.getElementById('target_type');

    const targetSelect =
        document.getElementById('target_id');

    const vendorInput =
        document.getElementById('vendor_id');

    const hoitInput =
        document.getElementById('hoit_id');


    const selectedConfig =
        @json($selectedConfig);

    const selectedApplication =
        @json($selectedApplication);

    const selectedState =
        @json($selectedState);

    const selectedTargetType =
        @json($selectedTargetType);

    const selectedTargetId =
        @json($selectedTargetId);


    const urls = {

        configs: @json(route(
            'admin.issue-routing-rules.dependencies.support-configs'
        )),

        applications: @json(route(
            'admin.issue-routing-rules.dependencies.applications'
        )),

        states: @json(route(
            'admin.issue-routing-rules.dependencies.states'
        )),

        targets: @json(route(
            'admin.issue-routing-rules.dependencies.targets'
        ))
    };


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    function resetSelect(
        select,
        placeholder = 'Select'
    ) {

        select.innerHTML =
            `<option value="">${placeholder}</option>`;

        select.disabled = true;
    }


    function enableSelect(select) {

        select.disabled = false;
    }


    function showLoading(id, show = true) {

        const element =
            document.getElementById(id);

        if (!element) {
            return;
        }

        element.classList.toggle(
            'hidden',
            !show
        );
    }


    async function getJson(url) {

        const response =
            await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

        if (!response.ok) {
            throw new Error(
                `HTTP ${response.status}`
            );
        }

        const result =
            await response.json();

        /*
         * Controller returns:
         *
         * {
         *    success: true,
         *    data: [...]
         * }
         */

        return result.data ?? result;
    }


    function addOption(
        select,
        value,
        text,
        selected = false
    ) {

        const option =
            document.createElement('option');

        option.value = value;

        option.textContent = text;

        option.selected = selected;

        select.appendChild(option);
    }


    /*
    |--------------------------------------------------------------------------
    | Load Support Configurations
    |--------------------------------------------------------------------------
    */

    async function loadSupportConfigs(
        projectId,
        selectedValue = ''
    ) {

        resetSelect(
            configSelect,
            'Select Support Configuration'
        );

        resetSelect(
            applicationSelect,
            'Select Application'
        );

        resetSelect(
            stateSelect,
            'Select State'
        );

        resetSelect(
            targetSelect,
            'Select Target'
        );

        if (!projectId) {
            return;
        }

        showLoading(
            'configLoading',
            true
        );

        try {

            const data =
                await getJson(
                    `${urls.configs}?project_id=${encodeURIComponent(projectId)}`
                );

            data.forEach(item => {

                addOption(
                    configSelect,
                    item.support_configuration_id,
                    `${item.configuration_code} - ${item.configuration_name}`,
                    String(item.support_configuration_id) === String(selectedValue)
                );

            });

            enableSelect(configSelect);

        } catch (error) {

            console.error(
                'Support configuration loading failed:',
                error
            );

        } finally {

            showLoading(
                'configLoading',
                false
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Load Applications
    |--------------------------------------------------------------------------
    */

    async function loadApplications(
        projectId,
        configId,
        selectedValue = ''
    ) {

        resetSelect(
            applicationSelect,
            'Select Application'
        );

        resetSelect(
            stateSelect,
            'Select State'
        );

        resetSelect(
            targetSelect,
            'Select Target'
        );

        if (!projectId) {
            return;
        }

        showLoading(
            'applicationLoading',
            true
        );

        try {

            let url =
                `${urls.applications}?project_id=${encodeURIComponent(projectId)}`;

            if (configId) {

                url +=
                    `&support_config_id=${encodeURIComponent(configId)}`;
            }

            const data =
                await getJson(url);

            data.forEach(item => {

                addOption(
                    applicationSelect,
                    item.application_id,
                    `${item.application_code} - ${item.application_name}`,
                    String(item.application_id) === String(selectedValue)
                );

            });

            enableSelect(
                applicationSelect
            );

        } catch (error) {

            console.error(
                'Application loading failed:',
                error
            );

        } finally {

            showLoading(
                'applicationLoading',
                false
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Load States
    |--------------------------------------------------------------------------
    */

    async function loadStates(
        applicationId,
        selectedValue = ''
    ) {

        resetSelect(
            stateSelect,
            'Select State'
        );

        resetSelect(
            targetSelect,
            'Select Target'
        );

        if (!applicationId) {
            return;
        }

        showLoading(
            'stateLoading',
            true
        );

        try {

            const data =
                await getJson(
                    `${urls.states}?application_id=${encodeURIComponent(applicationId)}`
                );

            data.forEach(item => {

                addOption(
                    stateSelect,
                    item.state_id,
                    `${item.state_code} - ${item.state_name}`,
                    String(item.state_id) === String(selectedValue)
                );

            });

            enableSelect(stateSelect);

        } catch (error) {

            console.error(
                'State loading failed:',
                error
            );

        } finally {

            showLoading(
                'stateLoading',
                false
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Load Vendor / HO IT
    |--------------------------------------------------------------------------
    */

    async function loadTargets(
        projectId,
        applicationId,
        stateId,
        type,
        selectedValue = ''
    ) {

        targetSelect.innerHTML =
            '<option value="">Select Target</option>';

        targetSelect.disabled = true;

        if (!type || !projectId) {
            return;
        }

        showLoading(
            'targetLoading',
            true
        );

        try {

            const url =
                `${urls.targets}` +
                `?project_id=${encodeURIComponent(projectId)}` +
                `&application_id=${encodeURIComponent(applicationId || '')}` +
                `&state_id=${encodeURIComponent(stateId || '')}`;

            const result =
                await getJson(url);

            let records = [];

            if (type === 'HOIT') {

                records =
                    result.hoits ?? [];

                records.forEach(item => {

                    addOption(
                        targetSelect,
                        item.hoit_id,
                        `${item.hoit_code} - ${item.hoit_name}`,
                        String(item.hoit_id) === String(selectedValue)
                    );

                });

            } else if (type === 'VENDOR') {

                records =
                    result.vendors ?? [];

                records.forEach(item => {

                    addOption(
                        targetSelect,
                        item.vendor_id,
                        `${item.vendor_code} - ${item.vendor_name}`,
                        String(item.vendor_id) === String(selectedValue)
                    );

                });
            }

            enableSelect(
                targetSelect
            );

        } catch (error) {

            console.error(
                'Routing target loading failed:',
                error
            );

        } finally {

            showLoading(
                'targetLoading',
                false
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Project Change
    |--------------------------------------------------------------------------
    */

    projectSelect.addEventListener(
        'change',
        function() {

            loadSupportConfigs(
                this.value
            );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Support Config Change
    |--------------------------------------------------------------------------
    */

    configSelect.addEventListener(
        'change',
        function() {

            loadApplications(
                projectSelect.value,
                this.value
            );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Application Change
    |--------------------------------------------------------------------------
    */

    applicationSelect.addEventListener(
        'change',
        function() {

            loadStates(
                this.value
            );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Target Type Change
    |--------------------------------------------------------------------------
    */

    targetType.addEventListener(
        'change',
        function() {

            const type =
                this.value;

            vendorInput.value = '';
            hoitInput.value = '';

            loadTargets(
                projectSelect.value,
                applicationSelect.value,
                stateSelect.value,
                type
            );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Target Change
    |--------------------------------------------------------------------------
    */

    targetSelect.addEventListener(
        'change',
        function() {

            vendorInput.value = '';
            hoitInput.value = '';

            if (
                targetType.value === 'HOIT'
            ) {

                hoitInput.value =
                    this.value;

            } else if (
                targetType.value === 'VENDOR'
            ) {

                vendorInput.value =
                    this.value;
            }
        }
    );


    /*
    |--------------------------------------------------------------------------
    | EDIT MODE
    |--------------------------------------------------------------------------
    */

    @if($isEdit || old('project_id'))

    const initialProject =
        @json($selectedProject);

    if (initialProject) {

        loadSupportConfigs(
                initialProject,
                selectedConfig
            )
            .then(function() {

                return loadApplications(
                    initialProject,
                    selectedConfig,
                    selectedApplication
                );

            })
            .then(function() {

                return loadStates(
                    selectedApplication,
                    selectedState
                );

            })
            .then(function() {

                if (selectedTargetType) {

                    targetType.value =
                        selectedTargetType;

                    return loadTargets(
                        initialProject,
                        selectedApplication,
                        selectedState,
                        selectedTargetType,
                        selectedTargetId
                    );
                }

            });

    }

    @endif


    /*
    |--------------------------------------------------------------------------
    | Submit Protection
    |--------------------------------------------------------------------------
    */

    const form =
        document.getElementById(
            'routingRuleForm'
        );

    const saveButton =
        document.getElementById(
            'saveRuleButton'
        );

    form.addEventListener(
        'submit',
        function() {

            saveButton.disabled = true;

            saveButton.classList.add(
                'opacity-70',
                'cursor-not-allowed'
            );

            saveButton.innerHTML =
                'Saving...';

        }
    );

});
</script>