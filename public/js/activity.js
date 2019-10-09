$(document).on("click",".view-log-details",function () {
    let id = this.value;
    console.log(id);

    $.ajax({
        'url'   : '/activity-details',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {'id':id},
        'cache' : false,
        success:function(result){
            $('.logs-content').html(result);
        },error:function(error){
            console.log(error.status);
        }
    });
});