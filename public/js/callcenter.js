function check_value()
{
    let i;
    for (i = 0; i < arguments.length; i++) {

        if($('#'+arguments[i]).val().length > 0){
            $('.'+arguments[i]).closest('div.'+arguments[i]).removeClass('has-error').find('.text-danger').remove();
        }
    }
}

$(document).on('submit','#add-call-center',function(form){
    form.preventDefault();

    let data = $('#add-call-center').serialize();

    $.ajax({
        'url'   : '/add-new-call-center',
        'type'  : 'POST',
        'data'  : data,
        'cache' : false,
        success: function(result){
            console.log(result);
            if(result.success == true)
            {
                setTimeout(function(){
                    $('#add-call-center').trigger('reset');
                    $('#callCenterModal').modal('toggle');
                    $.notify({
                            message: 'New Call Center Successfully Added!'
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

    check_value('callcenter','street_address','region','state','postal_code','city');
    return false;
});


$(document).on('click','.edit-callcenter',function () {
    let value = this.value;

    $.ajax({
        'url'   : '/get-call-center-value',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {'id' : value},
        'cache' : false,
        success: function (result) {
            console.log(result);
            $('#callcenter_value').val(result.id);
            $('#update_callcenter').val(result.name);
            $('#update_street_address').val(result.street);
            $("#update_region option[value='"+result.region+"']").prop('selected',true);
            $('#update_state').val(result.state);
            $('#update_postal_code').val(result.postalcode);
            $('#update_city').val(result.city);
        },error: function (error) {
            console.log(error.status);
        }
    });
});


$(document).on('submit','#edit-call-center',function (form) {
    form.preventDefault();

    let value = $('#edit-call-center').serialize();

    // console.log(value);
    $.ajax({
        'url'   : '/update-call-center-details',
        'type'  : 'POST',
        'data'  : value,
        'cache' : false,
        success: function (result) {

            if(result.success == true)
            {
                setTimeout(function(){
                    $('#edit-call-center').trigger('reset');
                    $('#edit_callCenterModal').modal('toggle');
                    $.notify({
                            message: 'New Call Center Successfully Added!'
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
        }
    });

    check_value('update_callcenter','update_street_address','update_region','update_state','update_postal_code','update_city');
    return false;
});

$(document).on('click','.delete-callcenter',function () {
    let value = this.value;

    $.ajax({
        'url'   : '/get-call-center-delete-value',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {'id' : value},
        'cache' : false,
        success: function (result) {
            $('#call_center_delete_id').val(result.id);
            $('.callcenter_name').text(result.name);
        },error: function (error) {
            console.log(error.status);
        }
    });
});

$(document).on('submit','#delete_form_call_center',function (form) {
    form.preventDefault();

    let data = $('#delete_form_call_center').serialize();
    console.log(data);

    $.ajax({
        'url'   : '/delete-call-center-details',
        'type'  : 'POST',
        'data'  : data,
        'cache' : false,
        success: function (result) {

            if(result.success == true)
            {
                setTimeout(function(){
                    $('#delete_call_center').modal('toggle');
                    $.notify({
                            message: 'Call Center Successfully Deleted!'
                        } ,{
                            type: 'success'
                        }
                    );

                    setTimeout(function(){
                        location.reload();
                    },1500);
                });
            }
        }
    });
});

$(document).on('change','#region',function(){
    let value = $('#region').val();
    let state = $('#state').val();
    let city = $('#city').val();

    if(state != null || city != null)
    {
        $('#state').html("<option></option>");
        $('#city').html("<option></option>");
    }

    $.ajax({
        'url'   : '/provinces',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {'id' : value},
        'cache' : false,
        success: function (result) {
            $('#state').html(result);

        },error: function (error) {
            console.log(error.status);
        }
    });
});

$(document).on('change','#state',function(){
    let value = $('#state').val();

    $.ajax({
        'url'   : '/city',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {'id' : value},
        'cache' : false,
        success: function (result) {
            $('#city').html(result);

        },error: function (error) {
            console.log(error.status);
        }
    });
});