$(document).ready(function () {

    console.log('ajax');

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

    $('#state_id').select2({
        placeholder: "Select State",
        width: '100%'
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

    //----------------------------------------------------------
    // Loading
    //----------------------------------------------------------

    function loading(select) {

        $(select).html(
            '<option value="">Loading...</option>'
        );

    }

    //----------------------------------------------------------
    // Reset
    //----------------------------------------------------------

    function clearDropdown(select,text){

        $(select).html(
            '<option value="">Select '+text+'</option>'
        );

    }

    //----------------------------------------------------------
    // State -> Service
    //----------------------------------------------------------

    $('#state_id').on('change', function () {

        let id=$(this).val();

        console.log('state_id>>>',id);

        clearDropdown('#project_id','Project');
        clearDropdown('#application_id','Application');
        clearDropdown('#module_id','Module');

        loading('#service_id');

        if(id==""){
            clearDropdown('#service_id','Service');
            return;
        }

        $.get("/ajax/services/"+id,function(response){

            let option='<option value="">Select Service</option>';

            $.each(response,function(index,row){

                option+='<option value="'+row.id+'">'+row.service_name+'</option>';

            });

            $('#service_id').html(option).trigger('change.select2');

        });


        

    });

    //----------------------------------------------------------
    // Service -> Project
    //----------------------------------------------------------

    $('#service_id').change(function(){

        let id=$(this).val();

        clearDropdown('#application_id','Application');
        clearDropdown('#module_id','Module');

        loading('#project_id');

        $.get("/ajax/projects/"+id,function(response){

            let option='<option value="">Select Project</option>';

            $.each(response,function(index,row){

                option+='<option value="'+row.id+'">'+row.project_name+'</option>';

            });

            $('#project_id').html(option).trigger('change.select2');

        });

    });

    //----------------------------------------------------------
    // Project -> Application
    //----------------------------------------------------------

    $('#project_id').change(function(){

        let id=$(this).val();

        clearDropdown('#module_id','Module');

        loading('#application_id');

        $.get("/ajax/applications/"+id,function(response){

            let option='<option value="">Select Application</option>';

            $.each(response,function(index,row){

                option+='<option value="'+row.id+'">'+row.application_name+'</option>';

            });

            $('#application_id').html(option).trigger('change.select2');

        });

    });

    //----------------------------------------------------------
    // Application -> Module
    //----------------------------------------------------------

    $('#application_id').change(function(){

        let id=$(this).val();

        loading('#module_id');

        $.get("/ajax/modules/"+id,function(response){

            let option='<option value="">Select Module</option>';

            $.each(response,function(index,row){

                option+='<option value="'+row.id+'">'+row.module_name+'</option>';

            });

            $('#module_id').html(option).trigger('change.select2');

        });

    });

    //----------------------------------------------------------
    // Character Counter
    //----------------------------------------------------------

    $('#subject').keyup(function(){

        $('#subjectCount').text(
            $(this).val().length
        );

    });

    $('#description').keyup(function(){

        $('#descriptionCount').text(
            $(this).val().length
        );

    });

    //----------------------------------------------------------
    // Attachment
    //----------------------------------------------------------

    $('#attachment').change(function(){

        let file=this.files[0];

        if(file){

            $('#selectedFile').html(
                '<strong>'+file.name+'</strong>'
            );

        }

    });

    //----------------------------------------------------------
    // Submit Button
    //----------------------------------------------------------

    $('#issueForm').submit(function(){

        $('#submitBtn')
            .prop('disabled',true)
            .html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

    });

});