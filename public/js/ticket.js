/* Get a Twilio Client token with an AJAX request */
$(document).ready(function() {


    //console.log(window.location.pathname);
    // $.ajax({
    //     'url'   : '/token',
    //     'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //     'type'  : 'POST',
    //     'data'  : {forPage: window.location.pathname},
    //     'cache' : false,
    //     success: function (result) {
    //         console.log(result.token);
    //     }
    // });

});

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
            console.log(result);
            if(result.success == true)
            {
                location.reload();
            }
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
            if(result.success == true)
            {
                location.reload();
            }
        }
    });
});

$(document).on('click','.twilio_call_back',function(){
    let ticket_id = this.value;

    let mobile_no = $('input[name=user_mobile_no'+ticket_id+']').val();

    $.ajax({
        'url'   : '/v1/events',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'cache' : false,
        success: function (result) {
            console.log(result);
        }
    });
});

/*$(document).on('click','.call_user',function(){
    let ticket_id = this.value;
    let mobile_no = $('input[name=user_mobile_no'+ticket_id+']').val();
    console.log('Calling User Now');
    //callCustomer(mobile_no);



    $.ajax({
        'url'   : '/v1/call-user',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {
            'ticket_id' : ticket_id,
            'mobile_no' : mobile_no,
        },
        'cache' : false,
        success: function (result) {
            let callId = result.sid;

            $.ajax({
                'url'   : '/v1/events',
                'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                'type'  : 'POST',
                'data'  : {
                    'sid' : callId,
                },
                'cache' : false,
                success: function (data) {
                    console.log(data)

                }
            });

        }
    });
});*/


$(document).on('click','.call_finish',function(){
    let ticket_id = this.value;

    let data = {
        'ticket_id' : ticket_id,
    };
    $.ajax({
        'url'   : '/update-ticket-after-call',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : data,
        'cache' : false,
        success: function (result) {
            if(result.success == true)
            {
                location.reload();
            }
        }
    });
   console.log("Call Finish " + ticket_id);
});

$(document).on('click','.call_user',function(){
    let ticket_id = this.value;

    $.ajax({
        'url'   : '/display-lead-details',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {
            'ticket_id' : ticket_id,
        },
        'cache' : false,
        success: function (result) {
            $('#lead-info-table #fullname').text(result.firstname+' '+result.lastname);
            $('#lead-info-table #mobile_no').text(result.mobile_no);
            $('#lead-info-table #address').text(result.city+', '+result.province+', '+result.region+' '+result.zip_code);
            $('#lead-info-table #lat').text(result.latitude);
            $('#lead-info-table #lang').text(result.longitude);
            $('#lead-info-table #request_date').text(result.request_date+' '+result.request_time);
            $('#lead-info-table #type_of_accident').text(result.type_of_accident);
            $('#lead-info-table #emergency_contact').text(result.emergency_contact);
            $('#lead-info-table #contact_no').text(result.contact_no);
        }
    });
});


/*
* connect_to_lgu
* */
$(document).on('click','.connect_to_lgu',function(){
    let ticket_id = this.value;

    $.ajax({
        'url'   : '/v1/connect-to-lgu',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {
            'ticket_id' : ticket_id,
        },
        'cache' : false,
        success: function (result) {
            if(result.success == true)
            {
                location.reload();
            }
        }
    });
});

$(document).on('click','.relate-ticket-btn',function(){
    let value = this.value;

    // console.log(value);
    $('#child-ticket-form input[name=ticketId]').val(value);
});

$(document).on('submit','#child-ticket-form', function(form){
    form.preventDefault();

    let data = $('#child-ticket-form').serialize();

    $.ajax({
        'url'   : '/relate-ticket',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : data,
        'cache' : false,
        success: function (result) {
            if(result.success == true)
            {
                location.reload();
            }

        }
    });
});
