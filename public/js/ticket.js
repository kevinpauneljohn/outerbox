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

$(document).on('click','button[name=select_lgu]',function(){
    let value = this.value;
    $('#lgu-form input[name=ticket_id]').val(value);
});

$(document).on('click','button[name=chosen_lgu]',function(form){
    form.preventDefault();

    let ticket_id = $('#ticket_id').val();
    let lgu_id = this.value;

    let data = {
        'ticket_id' : ticket_id,
        'lgu_id' : lgu_id
    }

    $.ajax({
        'url'   : '/assign-lgu-to-ticket',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : data,
        'cache' : false,
        success: function (result) {
            console.log(result);
        }
    });
});