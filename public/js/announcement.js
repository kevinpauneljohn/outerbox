function check_value()
{
    let i;
    for (i = 0; i < arguments.length; i++) {

        if($('#'+arguments[i]).val().length > 0){
            $('.'+arguments[i]).closest('div.'+arguments[i]).removeClass('has-error').find('.text-danger').remove();
        }
    }
}

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