function check_value()
{
    let i;
    for (i = 0; i < arguments.length; i++) {

        if($('#'+arguments[i]).val().length > 0){
            $('.'+arguments[i]).closest('div.'+arguments[i]).removeClass('has-error').find('.text-danger').remove();
        }
    }
}

/**
* this will create new announcement
 * @var data
* */
$(document).on('submit','#add-announcement-form',function (form) {
    form.preventDefault();

    let data = $('#add-announcement-form').serialize();

    $.ajax({
        'url'   : '/create-announcement',
        'type'  : 'POST',
        'data'  : data,
        'cache' : false,
        success:function(result){
            if(result.success == true)
            {
                setTimeout(function(){
                    $('#add-announcement-form').trigger('reset');
                    $('#add-announcement').modal('toggle');
                    $.notify({
                            message: 'New Announcement Successfully Added!'
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
        },error:function(error){
            console.log(error.status);
        }
    });
    check_value('title','description');
});

$(document).on('click','.view-announcement-detail',function () {
    let id = this.value;

    $.ajax({
        'url'   : '/display-announcement',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {'id' : id},
        'cache' : false,
        success:function(result){

            $('.announcement-title').text(result.title);
            $('.announcement-description').html(result.description);
        },error:function(error){
            console.log(error.status);
        }
    });

});
/*this will display the announcement details*/
$(document).on('click','.edit-announcement-detail',function () {

    let id = this.value;

    $.ajax({
        'url'   : '/display-announcement',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {'id' : id},
        'cache' : false,
        success:function(result){
            $('#announcementId').val(result.id);
            $('#edit_title').val(result.title);
            $('#edit_description ~ iframe').contents().find('.wysihtml5-editor').html(result.description);

        },error:function(error){
            console.log(error.status);
        }
    });
});

/*ajax call for submit announcement update*/
$(document).on('submit','#edit-announcement-form',function (form) {
    form.preventDefault();

    let data = $('#edit-announcement-form').serialize();

    $.ajax({
        'url'   : '/update-announcement',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : data,
        'cache' : false,
        success:function(result){
            console.log(result);

            if(result.success === true)
            {
                setTimeout(function(){
                    $('#edit-announcement-form').trigger('reset');
                    $('#edit-announcement').modal('toggle');
                    $.notify({
                            message: 'Announcement Successfully Updated!'
                        } ,{
                            type: 'success'
                        }
                    );

                    setTimeout(function(){
                        location.reload();
                    },1500);
                });
            }else if(result.error.length > 0)
            {
                setTimeout(function(){
                    $('.error-message').html('<div id="change_text" class="alert alert-warning">'+result.error+'</div>');

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

            return false;
        },error:function(error){
            console.log(error.status);
        }
    });
    check_value('edit_title','edit_description');
});

/*status approval*/
$(document).on('click','.status-update',function () {
    let data = this.value;

    $.ajax({
        'url'   : '/update-announcement-status',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {'data' : data},
        'cache' : false,
        success:function(result){
            console.log(result);

            if(result.success === true)
            {
                setTimeout(function(){
                    $.notify({
                            message: 'Announcement Status Updated!'
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
