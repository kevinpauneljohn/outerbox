@extends('layouts.lguTemplate')

@section('extra_meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')
LGU | Dashboard
@endsection
@section('extra_stylesheet')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<!-- Select2 -->
<link rel="stylesheet" href="{{asset('/bower_components/select2/dist/css/select2.min.css')}}">
<link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet">
@endsection
@section('page_header')
Dashboard
@endsection
@section('main_content')
    {{--{{ Request::segment(2) }}--}}
@endsection

@section('extra_script')
<!-- DataTables -->
<script src="{{asset('/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<!-- SlimScroll -->
<script src="{{asset('/bower_components/jquery-slimscroll/jquery.slimscroll.min.js')}}"></script>

<!-- FastClick -->
<script src="{{asset('/bower_components/fastclick/lib/fastclick.js')}}"></script>

<!-- growl notification -->
<script src="{{asset('bower_components/remarkable-bootstrap-notify/bootstrap-notify.min.js')}}"></script>

<script src="{{asset('/js/lgu.js')}}"></script>

<script>
    $(function () {
        $('#lgu-list').DataTable({
        })
    })
</script>
@endsection