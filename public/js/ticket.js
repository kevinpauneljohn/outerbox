/* Get a Twilio Client token with an AJAX request */
$(document).ready(function() {
    $.post('/token', {forPage: window.location.pathname}, function(data) {
        // Set up the Twilio Client Device with the token
        //Twilio.Device.setup(data.token);
        console.log(data.token)
    });

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

$(document).on('click','.call_user',function(){
    let ticket_id = this.value;
    let mobile_no = $('input[name=user_mobile_no'+ticket_id+']').val();

    //callCustomer(mobile_no);

    // $.ajax({
    //     'url'   : '/v1/call-user',
    //     'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //     'type'  : 'POST',
    //     'data'  : {
    //         'ticket_id' : ticket_id,
    //         'mobile_no' : mobile_no,
    //     },
    //     'cache' : false,
    //     success: function (result) {
    //         let callId = result.sid;
    //
    //         $.ajax({
    //             'url'   : '/v1/events',
    //             'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //             'type'  : 'POST',
    //             'data'  : {
    //                 'sid' : callId,
    //             },
    //             'cache' : false,
    //             success: function (data) {
    //                 console.log(data)
    //
    //             }
    //         });
    //
    //     }
    // });
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
    let lgu_id = this.value;

    $.ajax({
        'url'   : '/v1/connect-to-lgu',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : {
            'lgu_id' : lgu_id,
        },
        'cache' : false,
        success: function (result) {
            console.log(result);

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



/***********start twillio codes****************/
/**
 * Twilio Client configuration for the browser-calls-django
 * example application.
 */

// Store some selectors for elements we'll reuse
var callStatus = $("#call-status");
var answerButton = $(".answer-button");
var callSupportButton = $(".call-support-button");
var hangUpButton = $(".hangup-button");
var callCustomerButtons = $(".call-customer-button");

/* Helper function to update the call status bar */
function updateCallStatus(status) {
    callStatus.text(status);
}



/* Callback to let us know Twilio Client is ready */
Twilio.Device.ready(function (device) {
    updateCallStatus("Ready");
});

/* Report any errors to the call status display */
Twilio.Device.error(function (error) {
    updateCallStatus("ERROR: " + error.message);
});

/* Callback for when Twilio Client initiates a new connection */
Twilio.Device.connect(function (connection) {
    // Enable the hang up button and disable the call buttons
    hangUpButton.prop("disabled", false);
    callCustomerButtons.prop("disabled", true);
    callSupportButton.prop("disabled", true);
    answerButton.prop("disabled", true);

    // If phoneNumber is part of the connection, this is a call from a
    // support agent to a customer's phone
    if ("phoneNumber" in connection.message) {
        console.log(connection.message);
        updateCallStatus("In call with " + connection.message.phoneNumber);
    } else {
        // This is a call from a website user to a support agent
        updateCallStatus("In call with support");
    }
});

/* Callback for when a call ends */
Twilio.Device.disconnect(function(connection) {
    // Disable the hangup button and enable the call buttons
    hangUpButton.prop("disabled", true);
    callCustomerButtons.prop("disabled", false);
    callSupportButton.prop("disabled", false);

    updateCallStatus("Ready");
});

/* Callback for when Twilio Client receives a new incoming call */
Twilio.Device.incoming(function(connection) {
    updateCallStatus("Incoming support call");

    // Set a callback to be executed when the connection is accepted
    connection.accept(function() {
        updateCallStatus("In call with customer");
    });

    // Set a callback on the answer button and enable it
    answerButton.click(function() {
        connection.accept();
    });
    answerButton.prop("disabled", false);
});

/* Call a customer from a support ticket */
function callCustomer(phoneNumber) {
    updateCallStatus("Calling " + phoneNumber + "...");
    var callerID = "+6326263521";

    var params = {"phoneNumber": phoneNumber};
    //console.log(params);
    Twilio.Device.connect(params);
}

/* Call the support_agent from the home page */
function callSupport() {
    updateCallStatus("Calling support...");

    // Our backend will assume that no params means a call to support_agent
    Twilio.Device.connect();
}

/* End a call */
function hangUp() {
    Twilio.Device.disconnectAll();
}

/***********end twillio codes****************/
