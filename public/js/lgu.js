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
    console.log(data);
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
            else if(result.success == false){
                setTimeout(function(){
                    $('#change_status').html('<div id="change_text" class="alert alert-warning">'+result.success+'</div>');

                    setTimeout(function(){
                        $('#change_text').remove();
                    },3000);
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

    check_value('station_name','department','street_address','region','state','city','contactperson_fname','contactperson_lname','contactperson_no','call_center','contactperson_uname','password');
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

/**
* display the data on update form fields
* */
$(document).on("click",'.edit-lgu-btn',function(){
    let id = this.value;

    $.ajax({
        'url'   : '/display-lgu',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {'id':id},
        'cache' : false,
        success:function(result){

            $('#edit_station_name').val(result.stationName);
            $('#edit_department').val(result.department);
            $('#edit_street_address').val(result.address);
            $('#edit_region').val(result.region);
            $("#edit_region option[value='"+result.region+"']").prop('selected',true);
            ///$('#edit_state').val(result.province);
            $('#edit_state').html(result.province_value);
            $("#edit_state option[value='"+result.province+"']").prop('selected',true);
            ///$('#edit_city').val(result.city);
            $('#edit_city').html(result.city_value);
            $("#edit_city option[value='"+result.city+"']").prop('selected',true);
            $('#edit_postal_code').val(result.postalCOde);
            $('#edit_contactperson_fname').val(result.firstname);
            $('#edit_contactperson_lname').val(result.lastname);
            $('#edit_contactperson_no').val(result.contactNo);
            $('#lguId').val(result.lguId);
            $('#contactId').val(result.contactId);
            $('#contactPeopleId').val(result.contactPeopleId);
            $('#ccId').val(result.cc_id);

        },error:function(error){
            console.log(error.status);
        }
    });
});

$(document).on('submit','#edit-lgu-form',function(form){
    form.preventDefault();

    let data = $('#edit-lgu-form').serialize();

    $.ajax({
        'url'   : '/update-lgu',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : data,
        'cache' : false,
        success:function(result){
                console.log(result);
            if(result.success == true)
            {
                setTimeout(function(){
                    $('#edit-lgu-form').trigger('reset');
                    $('#edit-lgu').modal('toggle');
                    $.notify({
                            message: 'LGU details successfully updated!'
                        } ,{
                            type: 'success'
                        }
                    );

                    setTimeout(function(){
                        location.reload();
                    },1500);
                });
            }else{
                setTimeout(function(){
                    $('#change_status').html('<div id="change_text" class="alert alert-warning">'+result.success+'</div>');

                    setTimeout(function(){
                        $('#change_text').remove();
                    },3000);
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

    check_value('edit_station_name','edit_department','edit_street_address','edit_region','edit_state','edit_city','edit_postal_code','edit_contactperson_name','edit_contactperson_no');


        },error:function(error){
            console.log(error.status);
        }
    });
});


/*display LGU for delete*/
$(document).on("click",".delete-lgu-btn",function(){
    let id = this.value;

    $.ajax({
        'url'   : '/fetch-lgu-name',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {'id':id},
        'cache' : false,
        success:function(result){

        $(".lguName").text(result.station_name);
        $("#lgu-id").val(result.id);
        },error:function(error){
            console.log(error.status);
        }
    });
});

/*soft delete LGU*/
$(document).on("submit","#delete-lgu-form",function(form){
    form.preventDefault();
    let data = $("#delete-lgu-form").serialize();

    $.ajax({
        'url'   : '/delete-lgu',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : data,
        'cache' : false,
        success:function(result){
            if(result.success == true)
            {
                setTimeout(function(){
                    $('#delete-lgu').modal('toggle');
                    $.notify({
                            message: 'LGU successfully deleted!'
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