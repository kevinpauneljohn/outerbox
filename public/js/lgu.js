function check_value()
{
    let i;
    for (i = 0; i < arguments.length; i++) {

        if($('#'+arguments[i]).val().length > 0){
            $('.'+arguments[i]).closest('div.'+arguments[i]).removeClass('has-error').find('.text-danger').remove();
        }
    }
}

$(document).on('submit','#add-lgu',function(form){
    form.preventDefault();

    let data = $('#add-lgu').serialize();

    $.ajax({
        'url'   : '/add-lgu',
        'type'  : 'POST',
        'data'  : data,
        'cache' : false,
        success:function(result){
            console.log(result);

            if(result.success == true)
            {
                setTimeout(function(){
                    $('#add-lgu').trigger('reset');
                    $('#create-lgu').modal('toggle');
                    $.notify({
                            message: '1 LGU Successfully Added!'
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

    check_value('station_name','department','street_address','region','state','city','contactperson_name','contactperson_no');
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

/**
* edit the LGU details
* */
$(document).on('change','#edit_region',function(){
    let value = $('#edit_region').val();
    let state = $('#edit_state').val();
    let city = $('#edit_city').val();

    if(state != null || city != null)
    {
        $('#edit_state').html("<option></option>");
        $('#edit_city').html("<option></option>");
    }

    $.ajax({
        'url'   : '/provinces',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {'id' : value},
        'cache' : false,
        success: function (result) {
            $('#edit_state').html(result);

        },error: function (error) {
            console.log(error.status);
        }
    });
});

$(document).on('change','#edit_state',function(){
    let value = $('#edit_state').val();

    $.ajax({
        'url'   : '/city',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {'id' : value},
        'cache' : false,
        success: function (result) {
            $('#edit_city').html(result);

        },error: function (error) {
            console.log(error.status);
        }
    });
});
$(document).on("click",'.edit-lgu-btn',function(){
    let id = this.value;

    $.ajax({
        'url'   : '/update-lgu',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {'id':id},
        'cache' : false,
        success:function(result){
            console.log(result)

            $('#edit_station_name').val(result.stationName);
            $('#edit_department').val(result.department);
            $('#edit_street_address').val(result.address);
            $('#edit_region').val(result.region);
            $("#edit_region option[value='"+result.region+"']").prop('selected',true);
            $('#edit_state').val(result.province);
            $("#update_state option[value='"+result.province+"']").prop('selected',true);
            $('#edit_city').val(result.city);
            $("#update_city option[value='"+result.city+"']").prop('selected',true);
            $('#edit_contactperson_name').val(result.fullname);
            $('#edit_contactperson_no').val(result.contactNo);

        },error:function(error){
            console.log(error.status);
        }
    });
});