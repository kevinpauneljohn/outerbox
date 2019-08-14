function check_value()
{
    let i;
    for (i = 0; i < arguments.length; i++) {

        if($('#'+arguments[i]).val().length > 0){
            $('.'+arguments[i]).closest('div.'+arguments[i]).removeClass('has-error').find('.text-danger').remove();
        }
    }
}

/*add new role*/
$(document).on('submit','#role_form',function(form){
    form.preventDefault();
    var data = $('#role_form').serialize();

    $.ajax({
        'url'   : '/super-admin/roles',
        'type'  : 'POST',
        'data'  : data,
        'cache' : false,
        success: function (result) {

            if(result.success == true)
            {
                setTimeout(function(){
                    $('#role_form').trigger('reset');
                    $('#roles').modal('toggle');
                    $.notify({
                            message: 'New Role Successfully Added!'
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
    check_value('name','description');
});

/*set value to hidden input for deleting role*/
$(document).on('click','.delete-btn',function(){
    let value = this.value;

    $('#delete_form input[name=role]').val(value);
    $('#delete_form button[name=submit]').val(value);

    $.ajax({
        'url'   : '/roles-name',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {'id' : value},
        'cache' : false,
        success: function (result) {
            $('.role_name').text(result.role_name);
        }
    });
});

$(document).on('submit','#delete_form',function(form){
    form.preventDefault();
    let data = $('#delete_form').serialize();
    // console.log(data);

    $.ajax({
        'url'   : '/roles',
        'type'  : 'POST',
        'data'  : data,
        'cache' : false,
        success: function(result){
            if(result.success == true)
            {
                setTimeout(function(){
                    $('#delete').modal('toggle');
                    $.notify({
                            message: '1 Role Successfully Deleted!'
                        } ,{
                            type: 'success'
                        }
                    );

                    setTimeout(function(){
                        location.reload();
                    },1500);
                });
            }
        },error: function (result) {
            console.log(result.status);
        }
    });
});



// retrieve role details
$(document).on('click','.edit-btn',function(){
    let value = this.value;

    $.ajax({
        'url' : '/get-role-details',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {
            'id' : value
        },
        'cache'   : false,
        success : function (result) {
            $('#update_role #role_value').val(result.id);
            $('#update_role #edit_name').val(result.name);
            $('#update_role #edit_description').val(result.description);
            return false;
        },error: function (error) {
            console.log(error.status);
        }
    });
});


// update role
$(document).on('submit','#update_role',function(form){
    form.preventDefault();
    let data = $('#update_role').serialize();

    $.ajax({
        'url' : '/update-role-details',
        'type'  : 'POST',
        'data'  : data,
        'cache'   : false,
        success : function (result) {

            if(result.success == true)
            {
                setTimeout(function(){
                    $('#update_role').trigger('reset');
                    $('#edit_role').modal('toggle');
                    $.notify({
                            message: 'Role Successfully Updated!'
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
        },error: function (error) {
            console.log(error.status);
        }
    });
    check_value('edit_name','edit_description');
});

