<div class="grid grid-cols-1 gap-6 md:grid-cols-2">

    {{-- Project --}}
    <div>
        <label class="mb-2 block text-sm font-medium text-slate-700">
            Project <span class="text-red-500">*</span>
        </label>

        <select id="project_id" name="project_id" class="w-full rounded-xl border-slate-300" required>
            <option value="">Select Project</option>

            @foreach($projects as $project)
            <option value="{{ $project->project_id }}" @selected(old('project_id', $issueRoutingRule->project_id ?? '')
                == $project->project_id)
                >
                {{ $project->project_name }}
            </option>
            @endforeach
        </select>

        @error('project_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>


    {{-- Support Configuration --}}
    <div>
        <label class="mb-2 block text-sm font-medium text-slate-700">
            Support Configuration
        </label>

        <select id="support_config_id" name="support_config_id" class="w-full rounded-xl border-slate-300" disabled>
            <option value="">Select Support Configuration</option>
        </select>

        @error('support_config_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>


    {{-- Application --}}
    <div>
        <label class="mb-2 block text-sm font-medium text-slate-700">
            Application
        </label>

        <select id="application_id" name="application_id" class="w-full rounded-xl border-slate-300" disabled>
            <option value="">Select Application</option>
        </select>

        @error('application_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>


    {{-- State --}}
    <div>
        <label class="mb-2 block text-sm font-medium text-slate-700">
            State
        </label>

        <select id="state_id" name="state_id" class="w-full rounded-xl border-slate-300" disabled>
            <option value="">Select State</option>
        </select>

        @error('state_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

</div>

<div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-6">

    <div class="mb-5">
        <h3 class="text-base font-semibold text-slate-800">
            Routing Target
        </h3>

        <p class="mt-1 text-sm text-slate-500">
            Select where the issue should be routed.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

        {{-- Routing Level --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">
                Routing Level
            </label>

            <select name="routing_level" id="routing_level" class="w-full rounded-xl border-slate-300">
                <option value="1">Level 1</option>
                <option value="2">Level 2</option>
                <option value="3">Level 3</option>
            </select>
        </div>


        {{-- Target Type --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">
                Target Type
            </label>

            <select id="target_type" class="w-full rounded-xl border-slate-300">
                <option value="">Select Target</option>
                <option value="HOIT">HO IT</option>
                <option value="VENDOR">Vendor</option>
            </select>
        </div>


        {{-- Target --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">
                Target
            </label>

            <select id="target_id" class="w-full rounded-xl border-slate-300">
                <option value="">Select Target</option>
            </select>

            {{-- Actual database fields --}}
            <input type="hidden" name="hoit_id" id="hoit_id"
                value="{{ old('hoit_id', $issueRoutingRule->hoit_id ?? '') }}">

            <input type="hidden" name="vendor_id" id="vendor_id"
                value="{{ old('vendor_id', $issueRoutingRule->vendor_id ?? '') }}">
        </div>

    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {

    const projectSelect = document.getElementById('project_id');
    const configSelect = document.getElementById('support_config_id');
    const applicationSelect = document.getElementById('application_id');
    const stateSelect = document.getElementById('state_id');

    const targetType = document.getElementById('target_type');
    const targetSelect = document.getElementById('target_id');

    const hoitInput = document.getElementById('hoit_id');
    const vendorInput = document.getElementById('vendor_id');

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');


    function resetSelect(select, text = 'Select') {

        select.innerHTML =
            `<option value="">${text}</option>`;

        select.disabled = true;
    }


    function addOptions(select, data, valueKey, textKey) {

        data.forEach(item => {

            const option = document.createElement('option');

            option.value = item[valueKey];
            option.textContent = item[textKey];

            select.appendChild(option);
        });

        select.disabled = false;
    }


    /*
    |--------------------------------------------------------------------------
    | Project → Support Configuration
    |--------------------------------------------------------------------------
    */

    projectSelect.addEventListener('change', async function() {

        const projectId = this.value;

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

        try {

            const response = await fetch(
                `{{ route('admin.issue-routing-rules.dependencies.support-configs') }}?project_id=${projectId}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );

            const data = await response.json();

            addOptions(
                configSelect,
                data,
                'support_configuration_id',
                'configuration_name'
            );

        } catch (error) {

            console.error(
                'Support configuration loading failed',
                error
            );
        }
    });


    /*
    |--------------------------------------------------------------------------
    | Support Configuration → Application
    |--------------------------------------------------------------------------
    */

    configSelect.addEventListener('change', async function() {

        const projectId = projectSelect.value;
        const configId = this.value;

        resetSelect(
            applicationSelect,
            'Select Application'
        );

        resetSelect(
            stateSelect,
            'Select State'
        );

        if (!projectId) {
            return;
        }

        try {

            const url =
                `{{ route('admin.issue-routing-rules.dependencies.applications') }}` +
                `?project_id=${projectId}` +
                `&support_config_id=${configId}`;

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            addOptions(
                applicationSelect,
                data,
                'application_id',
                'application_name'
            );

        } catch (error) {

            console.error(
                'Application loading failed',
                error
            );
        }
    });


    /*
    |--------------------------------------------------------------------------
    | Application → State
    |--------------------------------------------------------------------------
    */

    applicationSelect.addEventListener('change', async function() {

        const applicationId = this.value;

        resetSelect(
            stateSelect,
            'Select State'
        );

        if (!applicationId) {
            return;
        }

        try {

            const url =
                `{{ route('admin.issue-routing-rules.dependencies.states') }}` +
                `?application_id=${applicationId}`;

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            addOptions(
                stateSelect,
                data,
                'state_id',
                'state_name'
            );

        } catch (error) {

            console.error(
                'State loading failed',
                error
            );
        }
    });


    /*
    |--------------------------------------------------------------------------
    | Target Type → Vendor / HO IT
    |--------------------------------------------------------------------------
    */

    targetType.addEventListener('change', async function() {

        const type = this.value;

        targetSelect.innerHTML =
            '<option value="">Select Target</option>';

        targetSelect.disabled = true;

        hoitInput.value = '';
        vendorInput.value = '';

        if (!type) {
            return;
        }

        const projectId = projectSelect.value;
        const applicationId = applicationSelect.value;
        const stateId = stateSelect.value;

        try {

            const url =
                `{{ route('admin.issue-routing-rules.dependencies.targets') }}` +
                `?project_id=${projectId}` +
                `&application_id=${applicationId}` +
                `&state_id=${stateId}`;

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            let records = [];

            if (type === 'HOIT') {
                records = data.hoits;

                records.forEach(item => {

                    const option =
                        document.createElement('option');

                    option.value = item.hoit_id;

                    option.textContent =
                        `${item.hoit_code} - ${item.hoit_name}`;

                    targetSelect.appendChild(option);
                });
            }

            if (type === 'VENDOR') {
                records = data.vendors;

                records.forEach(item => {

                    const option =
                        document.createElement('option');

                    option.value = item.vendor_id;

                    option.textContent =
                        `${item.vendor_code} - ${item.vendor_name}`;

                    targetSelect.appendChild(option);
                });
            }

            targetSelect.disabled = false;

        } catch (error) {

            console.error(
                'Routing target loading failed',
                error
            );
        }
    });


    /*
    |--------------------------------------------------------------------------
    | Target → Hidden DB Field
    |--------------------------------------------------------------------------
    */

    targetSelect.addEventListener('change', function() {

        const type = targetType.value;

        hoitInput.value = '';
        vendorInput.value = '';

        if (type === 'HOIT') {
            hoitInput.value = this.value;
        }

        if (type === 'VENDOR') {
            vendorInput.value = this.value;
        }
    });

});
</script>