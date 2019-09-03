function check_value()
{
    let i;
    for (i = 0; i < arguments.length; i++) {

        if($('#'+arguments[i]).val().length > 0){
            $('.'+arguments[i]).closest('div.'+arguments[i]).removeClass('has-error').find('.text-danger').remove();
        }
    }
}

$(document).on('submit','#add-staff',function (form) {
    form.preventDefault();

    let data = $('#add-staff').serialize();


    $.ajax({
        'url'   : '/add-employee',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : data,
        'cache' : false,
        success: function(result){

            console.log(result);
            if(result.success == true)
            {
                setTimeout(function(){
                    $('#add-staff').trigger('reset');
                    $('#create-staff').modal('toggle');
                    $.notify({
                            message: '1 Employee Successfully Added!'
                        } ,{
                            type: 'success'
                        }
                    );

                    setTimeout(function(){
                        location.reload();
                    },1500);
                });
            }

            $.each(result, function (key, value) {
                var element = $('#'+key);

                element.closest('div.'+key)
                    .addClass(value.length > 0 ? 'has-error' : 'has-success')
                    .find('.text-danger')
                    .remove();
                element.after('<p class="text-danger">'+value+'</p>');
            });
        },error: function (result) {
            console.log(result.status);
        }
    });
    check_value('firstname','lastname','email','username','password','role','callcenter');
});

$(document).on('click','.edit-employee',function(){
    let value = this.value;

    $.ajax({
        'url'   : '/get-employee-details',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {'id':value},
        'cache' : false,
        success:function(result){
            $('#user_value').val(result[0].id);
            $('#edit_firstname').val(result[0].firstname);
            $('#edit_middlename').val(result[0].middlename);
            $('#edit_lastname').val(result[0].lastname);
            $('#edit_email').val(result[0].email);
            $('#old_role').val(result[0].role_name);
            $('#edit_role').val(result[0].role_name).change();
            $("#edit_callcenter").val(result[0].cc_id).change();

        },error:function(error){
            console.log(error.status);
        }
    });
});

$(document).on('submit','#update-staff',function (form) {
    form.preventDefault();

    let data = $('#update-staff').serialize();


    $.ajax({
        'url'   : '/update-employee-details',
        'type'  : 'POST',
        'data'  : data,
        'cache' : false,
        success:function(result){
            console.log(result);
            if(result.success == true)
            {
                setTimeout(function(){
                    $('#edit_employee').modal('toggle');
                    $.notify({
                            message: '1 Employee Successfully Updated!'
                        } ,{
                            type: 'success'
                        }
                    );

                    setTimeout(function(){
                        location.reload();
                    },1500);
                });
            }

            $.each(result, function (key, value) {
                var element = $('#'+key);

                element.closest('div.'+key)
                    .addClass(value.length > 0 ? 'has-error' : 'has-success')
                    .find('.text-danger')
                    .remove();
                element.after('<p class="text-danger">'+value+'</p>');
            });
        },error:function(error){
            console.log(error.status);
        }
    });
    check_value('edit_firstname','edit_lastname','edit_email','edit_role','edit_callcenter');
});

$(document).on('click','.delete-employee-btn',function(){
    let value = this.value;
    $.ajax({
        'url'   : '/get-employee-details',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {'id':value},
        'cache' : false,
        success:function(result){
             $('.user_name').text(result[0].username);
             $('.user_delete').val(result[0].id);

        },error:function(error){
            console.log(error.status);
        }
    });
});

$(document).on('submit','#delete-staff',function(form){
    form.preventDefault();

    let data = $('#delete-staff').serialize();

    $.ajax({
        'url'   : '/delete-employee',
        'type'  : 'POST',
        'data'  : data,
        'cache' : false,
        success:function(result){
            if(result.success == true)
            {
                setTimeout(function(){
                    $('#delete_employee').modal('toggle');
                    $.notify({
                            message: '1 Employee Successfully Removed!'
                        } ,{
                            type: 'success'
                        }
                    );

                    setTimeout(function(){
                        location.reload();
                    },1500);
                });
            }

        },error:function(error){
            console.log(error.status);
        }
    });
});