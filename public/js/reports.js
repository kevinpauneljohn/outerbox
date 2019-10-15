/**
 * Oct. 16, 2019
 * @author john kevin paunel
 * this script will generate either pdf or excel file report
* */
$(document).on('click','.generate-report',function(){
    let action = this.value;
    let userId = $('#userId').val();
    let startDate = $('#start_date').val();
    let endDate = $('#end_date').val();
    // console.log(action);
    $.ajax({
        'url' : reportType(action),
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'GET',
        'data'  : {
            'action'    : action,
            'userId' : userId,
            'startDate' : startDate,
            'endDate'   : endDate,
        },
        'cache'   : false,
        success: function(result){
        },error: function (error) {
            console.log(error.status);
        }
    });
});

/**
 * @author john kevin paunel
 * Oct. 16, 2019
 * check if it will download pdf or excel file
 *@param action
 * @return string
* */

function reportType(value)
{
    let url ='';
    if(value == 'pdf')
    {
        url = '/generate-pdf-report';
    }else if (value == 'excel'){
        url = '/generate-excel-report';
    }

    return url;
}