$(document).on('submit','#add-lgu',function(form){
    form.preventDefault();

    let data = $('#add-lgu').serialize();

    $.ajax({
        'url'   : '/add-lgu',
        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        'type'  : 'POST',
        'data'  : data,
        'cache' : false,
        success:function(result){
            console.log(result);

        },error:function(error){
            console.log(error.status);
        }
    });
});