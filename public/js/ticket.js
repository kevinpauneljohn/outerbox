$(document).on('change','select[name=status]',function(){
    let value = this.value;
    let attr = this.id;

    let data = {
        'ticket_id' : attr,
        'value' : value
    };
    $.ajax({
        'url'   : '/update-ticket-status',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : data,
        'cache' : false,
        success: function (result) {

        }
    });
});