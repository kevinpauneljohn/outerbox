function check_value()
{
    let i;
    for (i = 0; i < arguments.length; i++) {

        if($('#'+arguments[i]).val().length > 0){
            $('.'+arguments[i]).closest('div.'+arguments[i]).removeClass('has-error').find('.text-danger').remove();
        }
    }
}

$(document).on('submit','#permission_form',function(form){
    form.preventDefault();
    var data = $('#permission_form').serialize();

    $.ajax({
        'url'   : '/super-admin/permissions',
        'type'  : 'POST',
        'data'  : data,
        'cache' : false,
        success: function (result) {

            if(result.success == true)
            {
                setTimeout(function(){
                    $('#permission_form').trigger('reset');
                    $('#permissions').modal('toggle');
                    $.notify({
                            message: 'New Permission Successfully Added!'
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
            return false;

        },error: function (result) {
            console.log(result.status);
        }
    });
    check_value('permission_name');
});


$(document).on('click','.edit_permission_btn',function(){
    let value = this.value;

    $.ajax({
        'url' : '/get-permission-details',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {
            'id' : value
        },
        'cache'   : false,
        success: function(result){
            $('#update_permission #permission_value').val(value);
            $('#update_permission #edit_permission_name').val(result.name);
        },error: function (error) {
            console.log(error.status);
        }
    });
    return false;
});

$(document).on('submit','#update_permission',function(form){
    form.preventDefault();
    let data = $('#update_permission').serialize();

    $.ajax({
        'url' : '/update-permission-details',
        'type'  : 'POST',
        'data'  : data,
        'cache'   : false,
        success: function(result){

            if(result.success == true)
            {
                setTimeout(function(){
                    $('#update_permission').trigger('reset');
                    $('#edit_permission').modal('toggle');
                    $.notify({
                            message: 'Permission Successfully Updated!'
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
                let element = $('#'+key);

                element.closest('div.'+key)
                    .addClass(value.length > 0 ? 'has-error' : 'has-success')
                    .find('.text-danger')
                    .remove();
                element.after('<p class="text-danger">'+value+'</p>');
            });
        },error: function (error) {
            console.log(error.status);
        }
    });
    check_value('edit_permission_name');
});

$(document).on('click','.delete_permission_btn',function () {
    let value = this.value;

    // console.log(value);
    $.ajax({
        'url' : '/get-permission-details',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {
            'id' : value
        },
        'cache'   : false,
        success: function(result){
            $('.delete_permission_name_confirm').text(result.name);
            $('.delete_permission_row').val(result.id);
        },error: function (error) {
            console.log(error.status);
        }
    });
    return false;
});

$(document).on('submit','#delete_form',function (form) {
    form.preventDefault();

    let data = $('#delete_form').serialize();
    $.ajax({
        'url' : '/delete-permission',
        'type'  : 'POST',
        'data'  : data,
        'cache'   : false,
        success: function(result){
            if(result.success == true)
            {
                setTimeout(function(){
                    $('#delete_permission').modal('toggle');
                    $.notify({
                            message: '1 Permission Successfully Deleted!'
                        } ,{
                            type: 'success'
                        }
                    );

                    setTimeout(function(){
                        location.reload();
                    },1500);
                });
            }
        },error: function (error) {
            console.log(error.status);
        }
    });
    return false;
});