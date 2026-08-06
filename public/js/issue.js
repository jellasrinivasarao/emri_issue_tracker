window.initializeRaiseIssueForm = function () {
    // Support both legacy `#raiseIssueForm` and current `#issueForm`
    let formSelector = null;

    if ($('#raiseIssueForm').length) {
        formSelector = '#raiseIssueForm';
    } else if ($('#issueForm').length) {
        formSelector = '#issueForm';
    }

    if (!formSelector) {
        return;
    }

    //----------------------------------------------------------
    // CSRF Token
    //----------------------------------------------------------

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //----------------------------------------------------------
    // Select2
    //----------------------------------------------------------

    if ($.fn.select2) {
        $('#state_id').select2({
            placeholder: "Select State",
            width: 'resolve',
            allowClear: true
        });

        $('#service_id').select2({
            placeholder: "Select Service",
            width: '100%'
        });

        $('#project_id').select2({
            placeholder: "Select Project",
            width: '100%'
        });

        $('#application_id').select2({
            placeholder: "Select Application",
            width: '100%'
        });

        $('#module_id').select2({
            placeholder: "Select Module",
            width: '100%'
        });

        $('#issue_category_id').select2({
            placeholder: "Select Issue Category",
            width: '100%'
        });

        $('#priority_id').select2({
            placeholder: "Select Priority",
            width: '100%'
        });
    } else {
        console.warn('Select2 is not loaded; dropdowns will remain plain selects.');
    }

    //----------------------------------------------------------
    // Loading
    //----------------------------------------------------------

    function loading(select) {
        $(select).html('<option value="">Loading...</option>');
    }

    //----------------------------------------------------------
    // Reset
    //----------------------------------------------------------

    function clearDropdown(select, text) {
        $(select).html('<option value="">Select ' + text + '</option>');
    }

    //----------------------------------------------------------
    // State -> Service
    //----------------------------------------------------------

    $('#state_id').off('change').on('change', function () {
        let id = $(this).val();

        console.log(id);

        clearDropdown('#project_id', 'Project');
        clearDropdown('#application_id', 'Application');
        clearDropdown('#module_id', 'Module');

        loading('#service_id');

        if (id === "") {
            clearDropdown('#service_id', 'Service');
            return;
        }

        $.get("/ajax/services/" + id, function (response) {
            let option = '<option value="">Select Service</option>';

            $.each(response, function (index, row) {
                option += '<option value="' + row.id + '">' + row.service_name + '</option>';
            });

            $('#service_id').html(option).trigger('change.select2');
        });
    });

    // If a state is already selected on load (e.g. edit form), load services
    (function loadServicesIfStateSelected() {
        let initialState = $('#state_id').val();

        if (initialState && initialState !== "") {
            // Trigger change to reuse the handler above
            $('#state_id').trigger('change');
        }
    })();

    // If state select has no usable options, fetch via AJAX
    (function loadStatesIfEmpty() {
        let hasUsableStateOption = $('#state_id option').filter(function () {
            return $(this).val() !== '' && $(this).val() !== undefined && $(this).val() !== null;
        }).length > 0;

        if (!hasUsableStateOption) {
            loading('#state_id');

            $.get('/ajax/states', function (response) {
                let option = '<option value="">Select State</option>';

                $.each(response, function (index, row) {
                    let stateId = row.id ?? row.state_id ?? '';
                    let stateName = row.state_name ?? row.name ?? '';

                    if (stateId !== '' && stateName !== '') {
                        option += '<option value="' + stateId + '">' + stateName + '</option>';
                    }
                });

                $('#state_id').html(option).trigger('change.select2');

                let initial = $('#state_id').data('initial');
                if (initial) {
                    $('#state_id').val(initial).trigger('change');
                }
            }).fail(function () {
                console.error('Unable to load states via AJAX');
                clearDropdown('#state_id', 'State');
            });
        }
    })();

    //----------------------------------------------------------
    // Service -> Project
    //----------------------------------------------------------

    $('#service_id').off('change').change(function () {
        let id = $(this).val();

        clearDropdown('#application_id', 'Application');
        clearDropdown('#module_id', 'Module');

        loading('#project_id');

        $.get("/ajax/projects/" + id, function (response) {
            let option = '<option value="">Select Project</option>';

            $.each(response, function (index, row) {
                option += '<option value="' + row.id + '">' + row.project_name + '</option>';
            });

            $('#project_id').html(option).trigger('change.select2');
        });
    });

    //----------------------------------------------------------
    // Project -> Application
    //----------------------------------------------------------

    $('#project_id').off('change').change(function () {
        let id = $(this).val();

        clearDropdown('#module_id', 'Module');

        loading('#application_id');

        $.get("/ajax/applications/" + id, function (response) {
            let option = '<option value="">Select Application</option>';

            $.each(response, function (index, row) {
                option += '<option value="' + row.id + '">' + row.application_name + '</option>';
            });

            $('#application_id').html(option).trigger('change.select2');
        });
    });

    //----------------------------------------------------------
    // Application -> Module
    //----------------------------------------------------------

    $('#application_id').off('change').change(function () {
        let id = $(this).val();

        loading('#module_id');

        $.get("/ajax/modules/" + id, function (response) {
            let option = '<option value="">Select Module</option>';

            $.each(response, function (index, row) {
                option += '<option value="' + row.id + '">' + row.module_name + '</option>';
            });

            $('#module_id').html(option).trigger('change.select2');
        });
    });

    //----------------------------------------------------------
    // Character Counter
    //----------------------------------------------------------

    $('#subject').off('keyup').keyup(function () {
        $('#subjectCount').text($(this).val().length);
    });

    $('#description').off('keyup').keyup(function () {
        $('#descriptionCount').text($(this).val().length);
    });

    //----------------------------------------------------------
    // Attachment
    //----------------------------------------------------------

    $('#attachment').off('change').change(function () {
        let file = this.files[0];

        if (file) {
            $('#selectedFile').html('<strong>' + file.name + '</strong>');
        }
    });

    //----------------------------------------------------------
    // Submit Button
    //----------------------------------------------------------

    $(formSelector).off('submit').submit(function () {
        $('#submitBtn')
            .prop('disabled', true)
            .html('<i class="fa fa-spinner fa-spin"></i> Submitting...');
    });
};

$(document).ready(function () {
    console.log('ajax');
    window.initializeRaiseIssueForm?.();
});